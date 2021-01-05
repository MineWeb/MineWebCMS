<?php

class MinecraftPing
{
    /*
     * Queries Minecraft server
     * Returns array on success, false on failure.
     *
     * WARNING: This method was added in snapshot 13w41a (Minecraft 1.7)
     *
     * Written by xPaw
     *
     * Website: http://xpaw.me
     * GitHub: https://github.com/xPaw/PHP-Minecraft-Query
     *
     * ---------
     *
     * This method can be used to get server-icon.png too.
     * Something like this:
     *
     * $Server = new MinecraftPing( 'localhost' );
     * $Info = $Server->Query();
     * echo '<img width="64" height="64" src="' . Str_Replace( "\n", "", $Info[ 'favicon' ] ) . '">';
     *
     */

    private $Socket;
    private $UDP = false;
    private $ServerAddress;
    private $ServerPort;
    private $Timeout;

    const RAKNET_MAGIC = "\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x12\x34\x56\x78";

    public function __construct($Address, $Port = 25565, $Timeout = 2, $udp = false, $ResolveSRV = true)
    {
        $this->ServerAddress = $Address;
        $this->ServerPort = (int)$Port;
        $this->Timeout = (int)$Timeout;
        $this->UDP = (bool)$udp;

        if ($ResolveSRV) {
            $this->ResolveSRV();
        }

        $this->Connect();
    }

    public function __destruct()
    {
        $this->Close();
    }

    public function Close()
    {
        if ($this->Socket !== null) {
            fclose($this->Socket);

            $this->Socket = null;
        }
    }

    public function Connect()
    {
        $connectTimeout = $this->Timeout;
        if ($this->UDP)
            $this->Socket = @fsockopen("udp://" . $this->ServerAddress, $this->ServerPort, $errno, $errstr, $connectTimeout);
        else
            $this->Socket = @fsockopen($this->ServerAddress, $this->ServerPort, $errno, $errstr, $connectTimeout);

        if (!$this->Socket) {
            $this->Socket = null;
            throw new MinecraftPingException("Failed to connect or create a socket: $errno ($errstr)");

        }
        // Set Read/Write timeout
        stream_set_timeout($this->Socket, $connectTimeout);
    }

    public function Query()
    {
        if ($this->UDP) {
            stream_set_blocking($this->Socket, true);
            $r1 = mt_rand(PHP_INT_MIN, PHP_INT_MAX);
            $r2 = mt_rand(PHP_INT_MIN, PHP_INT_MAX);
            $send = "\x01" . pack('J', $r1) . self::RAKNET_MAGIC . pack('J', $r2);
            stream_socket_sendto($this->Socket, $send);

            $buf = stream_socket_recvfrom($this->Socket, 2048);

            if (count($buf) < 1 || $buf[0] != "\x1c") {
                throw new MinecraftPingException("Unable to get a ping response.");
            }
            $ping_raw = substr($buf, strlen(self::RAKNET_MAGIC) + 19);
            $ping_datas = explode(';', $ping_raw);

            $Data['description'] = $ping_datas[1];
            $Data['version']['name'] = $ping_datas[0];
            $Data['players']['online'] = $ping_datas[4];
            $Data['players']['max'] = $ping_datas[5];
        } else {
            $TimeStart = microtime(true); // for read timeout purposes

            // See http://wiki.vg/Protocol (Status Ping)
            $Data = "\x00"; // packet ID = 0 (varint)

            $Data .= "\x04"; // Protocol version (varint)
            $Data .= Pack('c', StrLen($this->ServerAddress)) . $this->ServerAddress; // Server (varint len + UTF-8 addr)
            $Data .= Pack('n', $this->ServerPort); // Server port (unsigned short)
            $Data .= "\x01"; // Next state: status (varint)

            $Data = Pack('c', StrLen($Data)) . $Data; // prepend length of packet ID + data

            fwrite($this->Socket, $Data); // handshake
            fwrite($this->Socket, "\x01\x00"); // status ping

            $Length = $this->ReadVarInt(); // full packet length

            if ($Length < 10) {
                return false;
            }

            $this->ReadVarInt(); // packet type, in server ping it's 0

            $Length = $this->ReadVarInt(); // string length

            $Data = "";
            do {
                if (microtime(true) - $TimeStart > $this->Timeout) {
                    throw new MinecraftPingException('Server read timed out');
                }

                $Remainder = $Length - StrLen($Data);
                $block = fread($this->Socket, $Remainder); // and finally the json string
                // abort if there is no progress
                if (!$block) {
                    throw new MinecraftPingException('Server returned too few data');
                }

                $Data .= $block;
            } while (StrLen($Data) < $Length);

            if ($Data === false) {
                throw new MinecraftPingException('Server didn\'t return any data');
            }

            $Data = JSON_Decode($Data, true);

            if (JSON_Last_Error() !== JSON_ERROR_NONE) {
                if (Function_Exists('json_last_error_msg')) {
                    throw new MinecraftPingException(JSON_Last_Error_Msg());
                } else {
                    throw new MinecraftPingException('JSON parsing failed');
                }
                return false;
            }
        }
        return $Data;
    }

    public function QueryOldPre17()
    {
        fwrite($this->Socket, "\xFE\x01");
        $Data = fread($this->Socket, 512);
        $Len = StrLen($Data);

        if ($Len < 4 || $Data[0] !== "\xFF") {
            return false;
        }

        $Data = SubStr($Data, 3); // Strip packet header (kick message packet and short length)
        $Data = iconv('UTF-16BE', 'UTF-8', $Data);

        // Are we dealing with Minecraft 1.4+ server?
        if ($Data[1] === "\xA7" && $Data[2] === "\x31") {
            $Data = Explode("\x00", $Data);

            return [
                'HostName' => $Data[3],
                'Players' => IntVal($Data[4]),
                'MaxPlayers' => IntVal($Data[5]),
                'Protocol' => IntVal($Data[1]),
                'Version' => $Data[2]
            ];
        }

        $Data = Explode("\xA7", $Data);

        return [
            'HostName' => SubStr($Data[0], 0, -1),
            'Players' => isset($Data[1]) ? IntVal($Data[1]) : 0,
            'MaxPlayers' => isset($Data[2]) ? IntVal($Data[2]) : 0,
            'Protocol' => 0,
            'Version' => '1.3'
        ];
    }

    private function ReadVarInt()
    {
        $i = 0;
        $j = 0;

        while (true) {
            $k = @fgetc($this->Socket);

            if ($k === false) {
                return 0;
            }

            $k = Ord($k);

            $i |= ($k & 0x7F) << $j++ * 7;

            if ($j > 5) {
                throw new MinecraftPingException('VarInt too big');
            }

            if (($k & 0x80) != 128) {
                break;
            }
        }

        return $i;
    }

    private function ResolveSRV()
    {
        if (ip2long($this->ServerAddress) !== false) {
            return;
        }

        $Record = @dns_get_record('_minecraft._tcp.' . $this->ServerAddress, DNS_SRV);

        if (empty($Record)) {
            return;
        }

        if (isset($Record[0]['target'])) {
            $this->ServerAddress = $Record[0]['target'];
        }

        if (isset($Record[0]['port'])) {
            $this->ServerPort = $Record[0]['port'];
        }
    }
}

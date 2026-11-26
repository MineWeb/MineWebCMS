<?php

class ClientIp
{

    private static $cloudflareNetworks = [
        '173.245.48.0' => 20, '103.21.244.0' => 22, '103.22.200.0' => 22,
        '103.31.4.0' => 22, '141.101.64.0' => 18, '108.162.192.0' => 18,
        '190.93.240.0' => 20, '188.114.96.0' => 20, '197.234.240.0' => 22,
        '198.41.128.0' => 17, '162.158.0.0' => 15, '104.16.0.0' => 13,
        '104.24.0.0' => 14, '172.64.0.0' => 13, '131.0.72.0' => 22,
        '2400:cb00::' => 32, '2606:4700::' => 32, '2803:f800::' => 32,
        '2405:b500::' => 32, '2405:8100::' => 32, '2a06:98c0::' => 29,
        '2c0f:f248::' => 32
    ];

    public static function get()
    {
        if (empty($_SERVER['REMOTE_ADDR']) || !filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
            return '';
        }
        $remote = $_SERVER['REMOTE_ADDR'];
        if (empty($_SERVER['HTTP_CF_CONNECTING_IP']) || !filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
            return $remote;
        }

        $peer = inet_pton($remote);
        if (strlen($peer) === 16 && strncmp($peer, inet_pton('::ffff:0.0.0.0'), 12) === 0) {
            $peer = substr($peer, 12);
        }
        foreach (self::$cloudflareNetworks as $network => $bits) {
            $prefix = inet_pton($network);
            $bytes = (int)($bits / 8);
            if (strlen($peer) !== strlen($prefix) || strncmp($peer, $prefix, $bytes) !== 0) {
                continue;
            }
            $remaining = $bits % 8;
            if ($remaining === 0 || (ord($peer[$bytes]) >> (8 - $remaining)) === (ord($prefix[$bytes]) >> (8 - $remaining))) {
                return $_SERVER['HTTP_CF_CONNECTING_IP'];
            }
        }
        return $remote;
    }

}

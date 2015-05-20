<?php
/*
CaptchaComponent
*/
App::uses('Component', 'Controller');

class CaptchaComponent extends Component{
	
	public $settings = array(
		'characters' => null,
		'winHeight' => 50,
		'winWidth' => 320,
		'fontSize' => 25,
		'fontPath' => 'tahomabd.ttf',
		'bgNoise' => false,
		'lineNoise' => false,
		'bgColor' => '#F58220',
		'noiseColor' => '#000',
		'textColor' => '#fff',
		'noiseLevel' => '45'
		
	);


////////////////////////////////////////////////////////////////////////////////
	
	
	function __construct($collection, $settings){		
		$this->Controller = $collection->getController();		
	}

////////////////////////////////////////////////////////////////////////////////
	public function ShowImage($custom = array())
	{
		$new_settings = array_merge($this->settings, $custom);			
		
		$this->settings = $new_settings;
		$this -> win();		
	}

////////////////////////////////////////////////////////////////////////////////
	private function win()
	{
		//background image
		$image = imagecreatetruecolor($this->settings['winWidth'], $this->settings['winHeight']) 
        	or die("<b>" . __FILE__ . "</b><br />" . __LINE__ . " : 
            	" ."Cannot Initialize new GD image stream");
				
		$bgColor = $this->hex2rgb($this->settings['bgColor']);
		$noiseColor = $this->hex2rgb($this->settings['noiseColor']);
		$textColor = $this->hex2rgb($this->settings['textColor']);
		
		$bg = imagecolorallocate($image, $bgColor[0], $bgColor[1], $bgColor[2]);
		imagefill($image, 10, 10, $bg);
		
		for ($x=0; $x < $this->settings['noiseLevel']; $x++)
		{
			for ($y=0; $y < $this->settings['noiseLevel']; $y++)
			{
				$temp_color = imagecolorallocate($image,$noiseColor[0],$noiseColor[1],$noiseColor[2]);
				imagesetpixel( $image, rand(0,$this->settings['winWidth']), rand(0,$this->settings['winHeight']) , $temp_color );
			}
		}
		
		$char_color = imagecolorallocatealpha($image, $textColor[0], $textColor[1], $textColor[2], 0);

		//Font
		$font = $this->settings['fontPath'];
		
		$font_size = $this->settings['fontSize'];
		////////////////////////////////////
		//Image characters

		$char = "";
		if ( empty($this->settings['characters']) )
		{
			$this->settings['characters'] = mt_rand(100,10000);
		}
		$r_x1 = 10; $r_x2 = 20; 
		$r_y1 = $this->settings['winHeight']/1.8; $r_y2 = $r_y1+10; 
		
		
		$this->settings['characters'] = (string)$this->settings['characters'];
		
		for($i = 0; $i < strlen($this->settings['characters']); $i++ )
		{
			$char = $this->settings['characters'][$i];
			$random_x = mt_rand($r_x1 , $r_x2);
			$random_y = mt_rand($r_y1 , $r_y2);
			$random_angle = mt_rand(-20 , 20);
			imagettftext($image, $font_size, $random_angle, 
	        	$random_x, $random_y, $char_color, $font, $char);				
				
			$r_x1+=40; $r_x2+=40;	
		}
		

		////////////////////////////////////
		if ($this -> settings['bgNoise'])
			$image = $this -> apply_wave($image, $this->settings['winWidth'], 
            	$this->settings['winHeight']);
			
		////////////////////////////////////
		//lines
		if ($this -> settings['lineNoise'])
		{
			for ($i=0; $i<$this->settings['winWidth']; $i++ )
			{
				if ($i%10 == 0)
				{
					imageline ( $image, $i, 0, 
                    	$i+10, 50, $char_color );
					imageline ( $image, $i, 0, 
                    	$i-10, 50, $char_color );
				}
			}
		}
			
		////////////////////////////////////
		return imagepng($image);
		imagedestroy($image);
	}


///////////////////////////////////////////////////////////
	private function apply_wave($image, $width, $height)
	{		
		$x_period = 10;
		$y_period = 10;
		$y_amplitude = 5;
		$x_amplitude = 5;
		
		$xp = $x_period*rand(1,3);
		$k = rand(0,100);
		for ($a = 0; $a<$width; $a++)
			imagecopy($image, $image, $a-1, sin($k+$a/$xp)*$x_amplitude, 
            	$a, 0, 1, $height);
			
		$yp = $y_period*rand(1,2);
		$k = rand(0,100);
		for ($a = 0; $a<$height; $a++)
			imagecopy($image, $image, sin($k+$a/$yp)*$y_amplitude, 
            	$a-1, 0, $a, $width, 1);
		
		return $image;
	}
	
	
	private function hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);
	
	   if(strlen($hex) == 3) {
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb; // returns an array with the rgb values
	}
	
	
}




<?php

/*******************************************************************************
Version: 0.2 
Website: http://abhiomkar.in
Author: Abhinay Omkar abhiomkar@gmail.com
Title: SURBL Client 
Description: PHP Client Library for the surbl.org blacklists

Change Log:
v0.2
----
- using tlds list of 2 and 3 levels provided by surbl.org
- lot of improvements and bug fixes

v0.1
----
This is ported from surblclient of Python 

Licensed under The MIT License
Redistributions of files must retain the above copyright notice.
*******************************************************************************/


# SURBL SPAM Check - return True if it is Blacklisted at SURBL list
class Blacklist {

	public $url = "";
	public $spam_check = False;
	
	function __construct($url="") {
		$this->url = $url;
		
		$url_exploded = parse_url($this->url);
		$domain = $url_exploded['host'];

		$this->spam_check = $this->lookup($domain);
	}
	
	function _get_base_domain($domain) {
		# Remove User Info
	    if (strpos($domain, "@")){
	    	$domain = substr($domain, strpos($domain, "@") + 1, strlen($domain));
	    }
		
		# Remove Port    
	    if (strpos($domain, ":")){
	    	$domain = substr($domain, strpos($domain, ":") + 1, strlen($domain));
	    }
	
	    # Choose the right "depth"...
	    if ($this->_three_level_tlds($domain)) {
            # For any domain on the three level list, check it at the fourth level.
	    	$n = 4;
	    }
	    else if ($this->_two_level_tlds($domain)){
            # For any domain on the two level list, check it at the third level.
	    	$n = 3;
	    }
        else {
            # For any other domain, check it at the second level.
            $n = 2;
        }
	    
	    return implode('.', array_slice(explode('.', $domain), -$n));
	}
	
	function lookup($domain) {
		$_flags = array(
			2 => "sc",
			4 => "ws",
			8 => "ph",
			16 => "ob",
			32 => "ab",
			64 => "jp"
		  );
	
		$domain = $this->_get_base_domain($domain);
		
		$lookup = "$domain.multi.surbl.org";
		
		# returns the same host name if it couldn't resolve, otherwise, returns the IP Address
		$ip = gethostbyname($lookup);
        # Rudimentary way of validating IP Address, but this works.
		if (preg_match("/\d+\.\d+\.\d+\.\d+/", $ip)) {
			$last_octal_arr = array_slice(explode('.', $ip), -1);
			$last_octal = $last_octal_arr[0];
			$lists = array();
			foreach ($_flags as $key => $value) {
				if ($last_octal & $key) {
					$lists[] = $value;
				}
			}
			# SPAM SPAM! It's Blacklisted!
			return True;
		}
		else {
			# SAFE!
			return False;
		}
	}
	
	
    function _three_level_tlds($domain) {
        $three_level_tlds_data = file("three-level-tlds.data");

        foreach($three_level_tlds_data as $tld) {
            $tld = trim($tld);
            if($this->_ends_with($domain, $tld)) {
                return true;
            }
        }

        return false;
    }

    function _two_level_tlds($domain) {
        $two_level_tlds_data = file("two-level-tlds.data");

        foreach($two_level_tlds_data as $tld) {
            $tld = trim($tld);
            if($this->_ends_with($domain, $tld)) {
                return true;
            }
        }

        return false;
    }

    # Credits to http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions/834355#834355
    function _ends_with($haystack, $needle) {
        $length = strlen($needle);
        $start =  $length *-1; //negative
        return (substr($haystack, $start, $length) === $needle);
    }
    
}

# USAGE
# - Download 'two-level-tlds.data' & 'three-level-tlds.data' files to the same directory
# - the argument to Blacklist class should be a valid URL
/*
$url_c = new Blacklist("http://test.surbl.org");

if($url_c->spam_check) {
	echo "SPAM SPAM!";
}
else {
	echo "SAFE!";
}
*/

?>

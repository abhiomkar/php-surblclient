PHP Client Library for the surbl.org blacklists
===============================================

More details at: [http://www.surbl.org/guidelines](http://www.surbl.org/guidelines)

Usage:
-----

    require_once('surblclient.php');

    $url_c = new Blacklist("http://test.surbl.org");

    if($url_c->spam_check) {
        echo "SPAM SPAM!";
    }
    else {
        echo "SAFE!";
    }


This is ported from surblclient of Python. If you are looking for a Python SURBL Client: [https://github.com/infixfilip/surblclient](https://github.com/infixfilip/surblclient)

Blogs:
-------
[PHP Client Library for the surbl.org blacklists](http://blog.abhiomkar.in/2011/03/14/php-client-library-for-the-surbl-org-blacklists/)

Contributors:
-----
Abhinay Omkar  <<abhiomkar+nospam@gmail.com>>
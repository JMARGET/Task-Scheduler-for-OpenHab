<form action="action.php" method="post">
 <p>Your name: <input type="text" name="name" /></p>
 <p>Your age: <input type="text" name="age" /></p>
 <p><input type="submit" /></p>
</form>

<?php

class Foo {
    public $aMemberVar = 'aMemberVar Member Variable';
    public $aFuncName = 'aMemberFunc';
   
   
    function aMemberFunc() {
        
        return 'Inside the aMemberFunc';
    }
}

print "This spans
multiple lines. The newlines will be
output as well";

print "This spansmultiple lines. The newlines will be\noutput as well.";

$foo = new Foo;


$element = 'aMemberVar';
print $foo->$element . "\n"; // prints "aMemberVar Member Variable"
print $foo->aMemberFunc();

?>


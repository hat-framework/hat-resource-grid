<?php
function tabs(array $files)
{
$tabshead = <<<HEAD1
<div id="tabs"  style="font-size:75%">
    <ul>
        <li><a href="#DescriptionContent"><span>Info</span></a></li>
        <li><a href="#HTMLContent"><span>HTML</span></a></li>
        <li><a href="#PHPCode"><span>PHP</span></a></li>
    </ul>
    <div id="DescriptionContent" style="font-size:1.1em !important">
HEAD1;
echo $tabshead;
// info file
$filename = "info.txt";
$lines = file($filename);

// Loop through our array, show HTML source as HTML source; and line numbers too.
foreach ($lines as $line_num => $line) {
    echo $line . "<br />\n";
}
// html content
echo "</div>";
echo '<div id="HTMLContent" style="font-size:1.1em !important">';
$filename = "default.php";
highlight_file($filename);
echo "</div>";
// php code
echo '<div id="PHPCode" style= "font-size:1em !important">';
for($i=0;$i<count($files);$i++)
{
    $filename = $files[$i];
    echo '<span style="font-size:1.2em !important">';
    echo "<b>".$filename."</b>.<br />\n";
    if(strlen($filename)>0)
    {
        highlight_file($filename);
    }
    echo "<span>";
}
echo "</div>"; // php code
echo "</div>"; // tabs
echo '<script type="text/javascript">';
echo '$("#tabs").tabs();';
echo '</script>';
}
?>

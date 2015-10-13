<?php
class HtmlEscape{
	public static function escapeText($text, $tabWidth)
	{
		$text=str_replace("&","&amp;",$text);
		$text=str_replace("<","&lt;",$text);
		return $text;
		
	}
 	public static function unescapeText($text, $tabWidth)
	{
		$text=str_replace("&lt;","<",$text);
		$text=str_replace("&amp;","&",$text);
		
		return $text;
		
	}
     private static function tabsToSpaces($text, $tabWidth)
     {
         $spaces = "";
         $cursor = 0;
         for($i = 0; $i < strlen($text); $i++)
         {
             if($text[$i] == "\t")
             {
                 // Add spaces until the cursor position is divisible by 
                 // $tabWidth, adding at least one space so that if $cursor
                 // is already divisible by $tabWidth, we add $tabWidth spaces.
                 $spaces .= " ";
                 $cursor++;
                 while($cursor % $tabWidth != 0)
                 {
                     $spaces .= " ";
                     $cursor++;
                 }
             }
             else
             {
                 $spaces .= $text[$i];
                 $cursor++;
                 // Reset the cursor position to zero on CR or LF
                 if($text[$i] == "\n" || $text[$i] == "\r")
                     $cursor = 0;
             }
         }
         return $spaces;
     }
}
?>
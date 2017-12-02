<?php
class Validate_UInput
{
     const RegEMAIL = '/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]+$/';
     const RegINT = '/^[0-9]+$/';
     const RegDOUBLE = '/^[0-9]+\.*[0-9]*$/';
     const RegLETTERS = '^[a-zA-Z]+$';
     const RegNUMBERSOPERATION = '/^[0-9]+\.*[0-9]*\s*(\+|\-|\*|\/)+\s*[0-9]+\.*[0-9]*$/';
     const REGTEXTNONALLOWEDCHARS = '/<[^<]+?>/';
     private static $initialized = false;
     private static function initialize()
     {
          if (self::$initialized)
          {
               return;
          }
          self::$initialized = true;
     }
     static function GetClassConstants()
     {
          self::initialize();
          $oClass = new ReflectionClass(__CLASS__);
          return $oClass->getConstants();
     }
     //static public function Testfunc($pattern, $input)
     //{
     //     return false;
     //     var_dump(defined($pattern));
     //     echo '<br />';
     //     var_dump(self::GetClassConstants());
     //     if(in_array($pattern, self::GetClassConstants(), true))
     //     {
     //          echo '<br />';
     //          echo 'This is a Test';
     //     }
     //     
     //}
     static public function DoMathFromInput($input) //Funktion ist zu testen!
     {     
          self::initialize();
          $pattern = self::RegNUMBERSOPERATION;
          if(preg_match($pattern, $input) === 1)
          {
               
               $result = 0;
               $mathParams = array();
               if (strpos($input,'+') !== false )
               {
                    $mathParams = explode('+', $input);
                    for ($i = 0; count($mathParams) > $i; $i++)
                    {
                         if (empty($result))
                         {
                              $result = $mathParams[$i];
                         }
                         else
                         {
                              $result = $result + $mathParams[$i];
                         }
                    }
                    return $result;
               }
               elseif(strpos($input,'-') !== false)
               {
                    $mathParams = explode('-', $input);
                    for ($i = 0; count($mathParams) > $i; $i++)
                    {
                         if (empty($result))
                         {
                              $result = $mathParams[$i];
                         }
                         else
                         {
                              $result = $result - $mathParams[$i];
                         }
                    }
                    return $result;
               }
               elseif(strpos($input,'*' )!== false)
               {
                    $mathParams = explode('*', $input);
                    for ($i = 0; count($mathParams) > $i; $i++)
                    {
                         if (empty($result))
                         {
                              $result = $mathParams[$i];
                         }
                         else
                         {
                              $result = $result * $mathParams[$i];
                         }
                    }
                    return $result;
               }
               elseif(strpos($input,'/') !== false)
               {
                    $mathParams = explode('/', $input);
                    if($mathParams[1] != 0)
                    {
                    for ($i = 0; count($mathParams) > $i; $i++)
                    {
                         if (empty($result))
                         {
                              $result = $mathParams[$i];
                         }
                         else
                         {
                              $result = $result / $mathParams[$i];
                         }
                    }
                    }
                    return $result;
               }
          }
     }
     static public function ValidateWConst($pattern, $input)
     {
          self::initialize();
          if (in_array($pattern, self::GetClassConstants(), true))
          {
               if (preg_match($pattern, $input) === 1)
               {
                    return true;
               }
               elseif (preg_match($pattern, $input) === 0)
               {
                    return false;
               }
               else
               {
                   return false;
               }
          }
          else
          {
               return false;
          }
     }
     static public function Validate($pattern, $input)
     {
          self::initialize();
          if (preg_match($pattern, $input) === 1)
          {
               return true;
          }
          elseif (preg_match($pattern, $input) === 0)
          {
               return false;
          }
          else
          {
              return false;
          }
     }
     static public function Sanitize($input, $totrim, $is_input=false, $use_htmlentities=false) //Strings as in- and outputs
     {
          self::initialize();
          if(settype($input, "string"))
          {
               $sanConst = self::REGTEXTNONALLOWEDCHARS;
               if($is_input === false)
               {
                    if($totrim === true)
                    {
                         if($use_htmlentities)
                         {
                               return trim(htmlentities(preg_replace($sanConst,' ',strip_tags($input)), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8'));
                         }
                         else
                         {
                              return trim(preg_replace($sanConst,' ',strip_tags($input)));
                         }
                    }
                    else
                    {
                         if($use_htmlentities)
                         {
                               return htmlentities(preg_replace($sanConst,' ',strip_tags($input)), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8');
                         }
                         else
                         {
                              return preg_replace($sanConst,' ',strip_tags($input));
                         }
                    }
               }
               else
               {
                    if(preg_match($sanConst, $input) ===0 && preg_match($sanConst, $input) !==false )
                    {
                         if($totrim === true)
                         {
                              if($use_htmlentities)
                              {
                                   return trim(preg_replace($sanConst,' ',strip_tags($input)));
                              }
                              else
                              {
                                   return htmlentities(trim(preg_replace($sanConst,' ',strip_tags($input))),  ENT_QUOTES | ENT_DISALLOWED, 'UTF-8');
                              }
                         }
                         else
                         {
                              if($use_htmlentities)
                              {
                                    return preg_replace($sanConst,' ',strip_tags($input));
                              }
                              else
                              {
                                   return htmlentities(preg_replace($sanConst,' ',strip_tags($input)), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8');
                              }
                         }
                    }
                    else
                    {
                         return false;
                    }
               }
          }
          else
          {
               return '';
          }
     }
     
     //static public function Sanitize_Input($input, $totrim) //Strings as in- and outputs
     //{
     //     self::initialize();
     //     if(settype($input, "string"))
     //     {
     //          $sanConst = self::REGTEXTNONALLOWEDCHARS;
     //          if($totrim === true)
     //          {
     //                return trim(htmlentities(preg_replace($sanConst,' ',strip_tags($input)), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8'));
     //          }
     //          else
     //          {
     //                return htmlentities(preg_replace($sanConst,' ',strip_tags($input)), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8');
     //          }
     //     }
     //     else
     //     {
     //          return '';
     //     }
     //}
     
     static public function IsSanitized($input)
     {
          //does awkward stuff, hence not used
          return false;
		//needed?
          self::initialize();
          if(self::Sanitize($input, false) === $input)
          {
               return true;
          }
          else
          {
               return false;
          }
     }
}
?>
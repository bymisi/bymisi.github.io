<?php

// ************************************************************************
//PHPrcon - PHP script collection to remotely administrate and configure Halflife and HalflifeMod Servers through a webinterface
//Copyright (C) 2002  Henrik Beige
//
//This library is free software; you can redistribute it and/or
//modify it under the terms of the GNU Lesser General Public
//License as published by the Free Software Foundation; either
//version 2.1 of the License, or (at your option) any later version.
//
//This library is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
//Lesser General Public License for more details.
//
//You should have received a copy of the GNU Lesser General Public
//License along with this library; if not, write to the Free Software
//Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
// ************************************************************************

////////////////////////////////////////////////////////////
//
// ParseDir => Parse inventory of a directory into a choosebox
//
//   &$tpl => Reference the template instabce
//   $directoy => relative/absoulte path that should be parsed
//   $key => Name of the entry that should be selected
//   $blockname => Name of the template block (Block Variable = strtoupper(Blockname) )
//
////////////////////////////////////////////////////////////

function ParseDir(&$tpl, $directory, $key, $blockname)
{
  $blockname_big = strtoupper($blockname);

  //Parse in directory inventory
  $i = 0;
  if($dirhandle = opendir($directory))
  {
    
    while(false !== ($file = readdir($dirhandle)))
    {
      if($file != "." && $file != ".." && $file != "pics" && $file != "none")
      {
        $tpl->set_var(array(
          "NAME" => $file,
          "SELECTED" => ($file == $key)?"selected":""
        ));
        $tpl->parse($blockname_big, $blockname, true);
      } //if($file != "." && $file != ".." && $file != "pics" && $file != "none")
    } //while(false !== ($file = readdir($dirhandle)))
    closedir($dirhandle); 
    
  } //if ($dirhandle = opendir($directory))  
}


?>
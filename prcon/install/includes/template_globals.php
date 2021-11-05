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

//set template blocks
$tpl->set_block("tabpane", "colordownload", "DUMMY");
$tpl->set_block("tabpane", "colorglobals", "DUMMY");
$tpl->set_block("tabpane", "colorserver", "DUMMY");
$tpl->set_block("tabpane", "colorserverdetails", "DUMMY");
  
//parse template blocks
$tpl->parse("COLORDOWNLOAD", "colordownload");
$tpl->parse("COLORGLOBALS", "colorglobals");
$tpl->parse("COLORSERVER", "colorserver");
$tpl->parse("COLORSERVERDETAILS", "colorserverdetails");
?>
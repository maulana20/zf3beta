<?php
namespace Administration\Model;

class Password
{
	function encode($pass)
	{
		return md5($pass);
	}
}

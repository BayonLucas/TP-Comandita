<?php
interface ICrud
{
	public function Create();
	public function Read($id);
	public function Update($id);
	public function Delete($id);
}

<?php

function resetFUN(){
  session_start();
  unset($_SESSION['csv']);
  //unset($_SESSION['selectedFileName']);
  session_write_close();
}

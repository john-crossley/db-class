<?php

/**
 * Dump and die
 *
 * Useful for debugging output, this will dump
 * out the data and then kill the script.
 */
function dd($data) {
  echo "<pre>";
  print_r($data);
  die("</pre>");
}

<?php
/**
 * CSV file accessor class
 *
 * This class provides an Iterator to read CSV-files.
 * It can also be serialzed into a $_SESSION and pick up, wherever it left processing the file.
 *
 * Make sure to include this file, before accessing the session using session_start()!
 *
 * @author  Sirko Schindler
 * @version 1.0.0
 * @access   public
 */
class CSVReader implements Iterator {

  // serializable
  private $row;
  private $offset;
  private $path;
  private $separator;
  private $current;
  private $filePosition;

  // non-serializable
  private $handle;

  /**
   * Create a new Instance
   *
   * @param  string   $path     path to the CSV file
   * @param  string   $sep      field separator used in the CSV file
   * @access public
   */
  public function __construct( string $path, string $sep = ',' ) {
    $this->path = $path;
    $this->separator = $sep;
    $this->rewind();
  }

  /* -------------------- Iterator Interface -------------------- */

  /**
   * Reset the file access.
   *
   * Calling this function will reset the file access
   * and move the file pointer towards the start of the file
   *
   * @access public
   */
  public function rewind() {

    // if there was an existing file handler, close it
    if( isset( $handle ) ) {
      fclsoe( $handle );
    }

    // reset internal state
    $this->offset = 0;
    $this->row = -1;
    $this->filePosition = 0;

    // open the file and read the first value
    $this->openFile();
    $this->next();
  }

  /**
   * Get the current row from the CSV file.
   *
   * Multiple subsequent calls of this function will <i>not</i> read more rows by itself.
   * Call next() to retrieve the next row.
   *
   * @return  array   an array containing the fields of the current row
   * @access public
   */
  public function current() {
    return $this->current;
  }

  /**
   * Get the current row's row-number.
   *
   * @return  integer    the current row's row-number
   * @access public
   */
  public function key() {
    return $this->row;
  }

  /**
   * Read the next line from the CSV-file.
   *
   * @access public
   */
  public function next() {
    $this->row += 1;
    $this->current = fgetcsv($this->handle, 100000, "\t");
    $this->filePosition = ftell( $this->handle );

  }

  /**
   * Get the current row's row-number.
   *
   * @return  bool    are there any more rows within this file?
   * @access  public
   */
  public function valid() {
    return !empty( $this->current );
  }

  /* -------------------- (De)Serialize -------------------- */

  /**
   * Open a filehandler for the file given in path.
   *
   * @access  private
   */
  private function openFile() {
    if( isset( $this->path ) ) {
      $this->handle = fopen( $this->path, "r");
      fseek( $this->handle, $this->filePosition );
    }
  }

  /**
   * Get a list of (flat) serializable properties of this class.
   *
   * This method will be called when serializing an instance within a session object.
   *
   * @access  public
   */
  public function __sleep() {
    return [ 'row', 'offset', 'path', 'separator', 'current', 'filePosition' ];
  }

  /**
   * Recreate the full instance after being deserialized.
   *
   * This method will be called when deserializing an instance from a session object.
   *
   * @access  public
   */
  public function __wakeup() {
    $this->openFile();
  }
}

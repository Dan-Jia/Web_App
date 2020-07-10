
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="style.css" rel="stylesheet" type="text/css" href="css/style.css.php">
  <title>Data Management</title>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</head>

<body>
  <header>
  <duv id="Titel">Visualization Project Vis-1</div>
  <div id="subtitel">Computational and Data Science - SS2018</div>
  </header>

  <section>
    <nav>
      <ul>
        <h2>Navigation</h2><br>
        <li><a href="WebinterfaceDataMgt.php">Upload data files</a></li>
        <hr>
        <li><a href="gridDataUploadToDatabase.php">Start uploading grid data</a></li>
        <hr>
        <li><a href="WebinterfaceVisFilter.php">Query data from uploaded files</a></li>
      </ul>
    </nav>
    <article id="main">
      <h3>Upload tsv files:</h3>

<!-- CONVERT FILESIZE TO READABLE KB ... -->
<?php
function human_filesize($bytes, $decimals = 2)
{
    $size = array(' B',' KB',' MB',' GB',' TB',' PB',' EB',' ZB',' YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
} ?>

<!-- INCLUDE PHP FILES BUTTONS ARE BASED ON-->
<?php
include 'csvUploadToDatabase.php';
include 'deleteIndex.php';
include 'deleteWholeDatabase.php';
include 'reset.php';
include 'config.php';                                         // Konfiguration
$fileList= glob($dataTSVPathName."*.tsv");
for ($i=0;$i<sizeof($fileList);$i++) {
    $fileList[$i]=basename($fileList[$i]);
}

$findme='tsv';
session_start();
if (!isset($_SESSION['selectedFileName'])) {
    $_SESSION['selectedFileName']= 'nothing selected';
}//if
$filename=$_SESSION['selectedFileName'];
session_write_close();

if (isset($_POST['fileSel'])) {
    session_start();
    unset($_SESSION['csv']);
    $_SESSION['selectedFileName']=$_POST['fileSel'];
    session_write_close();
}//if

session_start();
$filename=$_SESSION['selectedFileName'];
session_write_close();
?>

<!-- LISTING ALL TSV FILES OF CENTRAL FOLDER -->
<form method="post">
<table id="tblData">
 <tr>
    <th></th>
    <th>File name</th>
    <th>Last modified</th>
    <th>File size</th>
 </tr>
 <?php for ($i = 0; $i < sizeof($fileList); $i++) {
    ?>
 <tr>
    <td><input type="radio" name="fileSel" value=<?php echo $fileList[$i] ?> <br></td>
    <td><?php echo $fileList[$i]?></td>
    <td><?php echo date("F d Y H:i:s.", filemtime($dataTSVPathName.$fileList[$i])) ?> <br/></td>
    <td><?php echo human_filesize(filesize($dataTSVPathName.$fileList[$i])); ?></td>
 <?php
} ?> </tr>
</table> </br>
<!-- BUTTONS TO SELECT / UPLOAD / REMOVE / RESET DB-->
<br>
<input type="submit" name="select" id="select" value="Select data file">
<input type="submit" name="upload" id="upload" value="Upload to database"
    <?php if ((strpos($filename, $findme) === false) || isset($_POST['clearAll'])) {
        ?> disabled="true" <?php
    } ?>   />
<input type="submit" name="delete" id="delete" value="Remove from database"
    <?php if ((strpos($filename, $findme) === false) || isset($_POST['clearAll'])) {
        ?> disabled="true" <?php
    } ?>   />
<input type="submit" name="clearAll" id="clearAll" value="Flush database">
</form>
</br>


<!-- RESPONSE OF LINKED PHP FILES TO BUTTON SELECTION -->
<div id="main">
<?php
session_start();
$filename=$_SESSION['selectedFileName'];
session_write_close();
echo 'Status: '.$filename.'<br />';

if (isset($_POST['upload'])) {
    print(csvUploadToDatabaseFUN($filename));
}//if

if (isset($_POST['delete'])) {
    session_start();
    unset($_SESSION['csv']);
    session_write_close();
    print(deleteIndexFUN($filename));
}//if

if (isset($_POST['clearAll'])) {
    session_start();
    unset($_SESSION['csv']);
    session_write_close();
    print(deleteAllFUN());
}//if
?>
</div>

</article>
</section>

</body>
</html>

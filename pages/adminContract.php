
<?php
    $contractId = null;
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        $contractId = filter_input(INPUT_GET, 'contractId');
        echo "Your Contract ID: ".$contractId;
    }
?>
<!--<iframe src="/test.pdf#toolbar=0" width="100%" height="500px">-->
<!--</iframe>-->
<form action="uploadFile.php" method="post" enctype="multipart/form-data">
    <label for="file">Filename:</label><input type="file" name="uploadedFile" id="file" />
    <input type="hidden" name="contractID" value="<?php echo $contractId?>"/>
    <p><input type="submit" name="submit" value="submit"/></p>
</form>
<p><a href="readingDir.php?extensionName=png">View Your Contract</a></p>

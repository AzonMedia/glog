<?php

namespace Azonmedia\Glog\Home\Views;

use Guzaba2\Mvc\PhpView;
use Psr\Http\Message\ResponseInterface;

class Home extends PhpView
{
    public function view() : void
    {
        $structure = $this->Response->getBody()->getStructure();//no reference as it is only for reading
        ?>
<h3><?=$structure['message']?></h3>
        <?php
    }

/*
Test for uploading files

    public function create() : void
    {
        $structure = $this->Response->getBody()->getStructure();//no reference as it is only for reading
        ?>
<h1><?=$structure['message']?></h1>

<?php
	if (isset($structure['uploaded_files_messages']) && !empty($structure['uploaded_files_messages'])) {
		foreach ($structure['uploaded_files_messages'] as $m) {
			echo $m;
		}
	}
?>

<form method="post" enctype="multipart/form-data">
	Select multiple files: <input type="file" name="my_file1[]" multiple ><br />
	Select One file: <input type="file" name="my_file2"><br />
	<input type="submit" value="Submit">
</form>
        <?php
    }
*/
}
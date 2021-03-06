<?
	include('init.php');

	if ($_POST[done]){

		$data = trim($_POST[data]);

		if (strlen($data)){

			$id = db_insert('reports', array(
				'date_create'	=> time(),
				'user'		=> AddSlashes('_TEMP_'),
				'data'		=> AddSlashes($data),
			));

			$day = parse_raid_date($id, $data);

			if (!$day){
				$page_title = 'Error - Bad XML';
				include('head.txt');
?>
	<p>It looks like that XML wasn't in the usual HeadCount format. <a href="import.php">Try again</a>.</p>

	<p>	To get the correct format:
		Right click the headcount button around the minimap and go into Exporting-&gt;Export Format and choose XML.
		After that, go back to the raid and hit Export, CTRL-C and then CTRL-V it into the import form.
	</p>

<?
				include('foot.txt');
				exit;
			}

			header("location: parse.php?d=$day");
			exit;
		}
	}

	$page_title = 'Import Raid Info';
	$nav = 'import';

	include('head.txt');
?>

<p>Just copy and paste the XML output from Headcount into the box below:</p>

<form action="import.php" method="post">
<input type="hidden" name="done" value="1" />

<p><textarea name="data" wrap="virtual" style="width: 100%; height: 400px;"></textarea></p>

<p><input type="submit" value="Import Raid" /></p>

</form>

<?
	include('foot.txt');
?>
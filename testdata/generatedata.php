<?
// script to generate test data



$classPrefix = array("anth" => "Anthropology",
					"bio" => "Biology",
					"cmps" => "Computer Science",
					"econ" => "Economics",
					"fem" => "Feminist Studies",
					"heb" => "Hebrew",
					"ism" => "Information System Management",
					"jap" => "Japanese",
					"kre" => "Kresge",
					"lin" => "Linguistics",
					"mar" => "Marine Biology",
					"neu" => "Neuro Psychology",
					"oce" => "Oceanography",
					"psych" => "Psychology",
					"soc" => "Sociology",
					"thea" => "Theatre Arts",
					"zoo" => "Zoology"
				);
$classNumberMax=200;
$datacolumns = 6;
$schoolName="Test University";


$outFile="testdata.csv";
if(!$outFilePointer = fopen($outFile, "w")){
	echo "Bad Out File\n";
	exit();
}

$classList = array();

foreach($classPrefix as $department => $departmentName){
	$i = 15;
	$classNumbers = array();
	while($i){
		$random = mt_rand(1, $classNumberMax);
		if(!in_array($random, $classNumbers)){
			$classNumbers[] = $random;
		}
		$i--;
	}
	sort($classNumbers);
	
	foreach($classNumbers as $number){
		$prereqs = array();
		for($i = 0; $i < 3; $i++){
			$prereq = generatePrereq($classNumbers, $number);
			if($prereq && !in_array($prereq, $prereqs)){
				$prereqs[] = $prereq;
			}
		}
		$classInfo = array($department . $number, $departmentName . " " . $number, $departmentName);
		if($number < 150){
			$classInfo[] = $departmentName . " Test Curriculum";
		} else {
			$classInfo[] = "";
		}
		foreach($prereqs as $prereq){
			$classInfo[] = $department . $prereq;
		}
		$classInfo = array_pad($classInfo, 7, '');
		$classList[] = $classInfo;
	}
}
writeLine($outFilePointer, array($schoolName));
$writeCount = 0;
foreach($classList as $row){
	writeLine($outFilePointer, $row);
	$writeCount++;
}
echo "Wrote " . $writeCount . " classes.";

function generatePrereq($classNumbers, $classNumber){
	if(mt_rand(0, 5)){
		$count = 5;
		while($count){
			$prereq = $classNumbers[array_rand($classNumbers)];
			if($prereq < $classNumber){
				return $prereq;
			}
			$count--;
		}
	}
	return false;
}

function writeLine($filePointer, $array){
	fwrite($filePointer, '"' . implode('","', $array) . '"' . "\n");
}

?>
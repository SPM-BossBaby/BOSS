<?php
require_once 'update_bid.php';
require_once 'common.php';

function doBootstrap($json = FALSE) {
		

	$errors = array();
	$lines = array();
	# need tmp_name -a temporary name create for the file and stored inside apache temporary folder- for proper read address
	$zip_file = $_FILES["bootstrap-file"]["tmp_name"];

	# Get temp dir on system for uploading
	$temp_dir = sys_get_temp_dir();

	#region keep track of number of lines successfully processed for each file
	$student_processed = 0;
	$course_processed = 0;
	$section_processed = 0;
	$prerequisite_processed = 0;
	$course_completed_processed = 0;
	$bid_processed = 0;

	$bid_errors = array();
	$course_errors = array();
	$course_completed_errors = array();
	$prerequisite_errors = array();
	$section_errors = array();
	$student_errors = array();
	#endregion

	# check file size
	if ($_FILES["bootstrap-file"]["size"] <= 0)
		$errors[] = "input files not found";
	else {
		
		$zip = new ZipArchive;
		$res = $zip->open($zip_file);

		if ($res === TRUE) {
			$zip->extractTo($temp_dir);
			$zip->close();
		
			#region Declare all your file paths
			$student_path = "$temp_dir/student.csv";
			$student = @fopen($student_path, "r");

			$course_path = "$temp_dir/course.csv";
			$course = @fopen($course_path, "r");

			$section_path = "$temp_dir/section.csv";
			$section = @fopen($section_path, "r");

			$prerequisite_path = "$temp_dir/prerequisite.csv";
			$prerequisite = @fopen($prerequisite_path, "r");

			$course_completed_path = "$temp_dir/course_completed.csv";
			$course_completed = @fopen($course_completed_path, "r");
			
			$bid_path = "$temp_dir/bid.csv";
			$bid = @fopen($bid_path, "r");	
			#endregion
			
			#region Check if files are empty, if empty display error message
			if (empty($student) || empty($course) || empty($section) || empty($prerequisite) || empty($course_completed) || empty($bid)){
				$errors[] = "input files not found";
				
				if (!empty($student)) {
					fclose($student);
					@unlink($student_path);
				}

				if (!empty($course)) {
					fclose($course);
					@unlink($course_path);
				}

				if (!empty($section)) {
					fclose($section);
					@unlink($section_path);
				}

				if (!empty($prerequisite)) {
					fclose($prerequisite);
					@unlink($prerequisite_path);
				}

				if (!empty($course_completed)) {
					fclose($course_completed);
					@unlink($course_completed_path);
				}

				if (!empty($bid)) {
					fclose($bid);
					@unlink($bid_path);
				}
			}
			#endregion
            
			else {

				#region Declare all DAOs
				$bidding_roundDAO = new BiddingRoundDAO();
				$studentDAO = new StudentDAO();
				$courseDAO = new CourseDAO();
				$sectionDAO = new SectionDAO();
				$prerequisiteDAO = new PrerequisiteDAO();
				$course_completedDAO = new CourseCompletedDAO();
				$bidDAO = new BidDAO();
				$enrolledDAO = new EnrolledDAO();
				$bidding_roundDAO = new BiddingRoundDAO();
				#endregion

				$connMgr = new ConnectionManager();
				$conn = $connMgr->getConnection();

				#start processing

				#region reset round
				$roundstart = date('Y-m-d H:i:s');
				$removeall = $bidding_roundDAO->removeAll();				
				$isStartOK = $bidding_roundDAO->startRound(new BiddingRound(1, 1, $roundstart, $end = NULL));				

				$enrolledDAO->removeAll();
				#endregion

				#region student Table
				# truncate current SQL tables
				$studentDAO->removeAll();
				$studentLineCount = 1;

				//skipping the header
				$header = fgetcsv($student);

				//check if it is at the last line else, continue with the while loop
				while (($data = fgetcsv($student)) != false){
					$studentLineCount++;

					#region validation of student data
					$current_error = array();
					
					$isEmptyLine = checkEmptyLine($data, $header);

					$data_userid = trim($data[0]);
					$data_password = trim($data[1]);
					$data_name = trim($data[2]);
					$data_school = trim($data[3]);
					$data_edollar = trim($data[4]);

					if(!empty($isEmptyLine)){
						$current_error = $isEmptyLine;
					} else {
						if(strlen($data_userid) > 128){
							array_push($current_error, "invalid userid");
						}
						if($studentDAO->getStudent($data_userid) != null){
							array_push($current_error, "duplicate userid");
						}
						// check if amount is less than 0 and more than 2 decimal 
						if(!is_numeric($data_edollar) || $data_edollar < 0.0 || preg_match('/\.\d{3,}/', $data_edollar)){
							array_push($current_error, "invalid e-dollar");
						}
						if(strlen($data_password) > 128){
							array_push($current_error, "invalid password");
						}
						if(strlen($data_name) > 100){
							array_push($current_error, "invalid name");
						}
					}
					#endregion

					if(empty($current_error)){
						$studentDAO->addStudent(new Student($data_userid, $data_password, $data_name, $data_school, $data_edollar));
						$student_processed++;
					}else{
						array_push($student_errors, [
							"file" => "student.csv",
							"line" => $studentLineCount, 
							"message" => $current_error
						]);
					}
				}

				fclose($student);
				@unlink($student_path);
				#endregion

				#region course Table
				$courseDAO->removeAll();
				$courseLineCount = 1;
				
				$header = fgetcsv($course);

				while (($data = fgetcsv($course)) != false){

					$courseLineCount++;

					#region validation of course data
					$current_error = array();
					
					$isEmptyLine = checkEmptyLine($data, $header);

					$data_course = trim($data[0]);
					$data_school = trim($data[1]);
					$data_title = trim($data[2]);
					$data_description = trim($data[3]);
					$data_examDate = trim($data[4]);
					$data_examStart = trim($data[5]);
					$data_examEnd = trim($data[6]);

					if(!empty($isEmptyLine)){
						$current_error = $isEmptyLine;
					} else {
						$examDate = DateTime::createFromFormat('Ymd', $data_examDate);
						if(!empty($data_examDate) && !($examDate && $examDate->format('Ymd') === $data_examDate)){
							array_push($current_error, "invalid exam date");
						}
						$examStart = DateTime::createFromFormat('G:i', $data_examStart);
						if(!empty($data_examStart) && !($examStart && $examStart->format('G:i') === $data_examStart)){
							array_push($current_error, "invalid exam start");
						}
						$examEnd = DateTime::createFromFormat('G:i', $data_examEnd);
						if((!empty($data_examEnd) && !($examEnd && $examEnd->format('G:i') === $data_examEnd)) || $examEnd <= $examStart){
							array_push($current_error, "invalid exam end");
						}
						if(strlen($data_title) > 100){
							array_push($current_error, "invalid title");
						}
						if(strlen($data_description) > 1000){
							array_push($current_error, "invalid description");
						}
					}
					#endregion

					if(empty($current_error)){
						$courseDAO->addCourse(new Course($data_course, $data_school, $data_title, $data_description, $data_examDate, $data_examStart, $data_examEnd));
						$course_processed++;
					}else{
						array_push($course_errors, [
							"file" => "course.csv",
							"line" => $courseLineCount,
							"message" => $current_error
						]);
					}
				}

				fclose($course);
				@unlink($course_path);
				#endregion

				#region section Table
				$sectionDAO->removeAll();
				$sectionLineCount = 1;
				
				$header = fgetcsv($section);

				while (($data = fgetcsv($section)) != false){

					$sectionLineCount++;

					#region validation of course data
					$current_error = array();
					
					$isEmptyLine = checkEmptyLine($data, $header);

					$data_course = trim($data[0]);
					$data_section = trim($data[1]);
					$data_day = trim($data[2]);
					$data_start = trim($data[3]);
					$data_end = trim($data[4]);
					$data_instructor = trim($data[5]);
					$data_venue = trim($data[6]);
					$data_size = trim($data[7]);

					if(!empty($isEmptyLine)){
						$current_error = $isEmptyLine;
					} else {
						if($courseDAO->getCourse($data_course) == null){
							array_push($current_error, "invalid course");
						} else {
							if (substr($data_section, 0, 1) != 'S' || !is_numeric(substr($data_section, 1)) || ((int)substr($data_section, 1) > 99 || (int)substr($data_section, 1) <= 0) || strlen((string)(int)substr($data_section, 1)) != strlen(substr($data_section, 1))){
								array_push($current_error, "invalid section");
							}
						}
						if(strlen($data_day) != 0 && !((1 <= (int)$data_day) && (7 >= (int)$data_day))){
							array_push($current_error, "invalid day");
						}
						$start = DateTime::createFromFormat('G:i', $data_start);
						if(!($start && $start->format('G:i') === $data_start)){
							array_push($current_error, "invalid start");
						}
						$end = DateTime::createFromFormat('G:i', $data_end);
						if(!($end && $end->format('G:i') === $data_end) || $end <= $start){
							array_push($current_error, "invalid end");
						}
						if(strlen($data_instructor) > 100){
							array_push($current_error, "invalid instructor");
						}
						if(strlen($data_venue) > 100){
							array_push($current_error, "invalid venue");
						}
						if(!preg_match('/^[0-9]+$/', $data_size) || (int)$data_size <= 0){
							array_push($current_error, "invalid size");
						}
					}
					
					#endregion

					if(empty($current_error)){
						$sectionDAO->addSection(new Section($data_course, $data_section, $data_day, $data_start, $data_end, $data_instructor, $data_venue, $data_size, 10));
						$section_processed++;
					}else{
						array_push($section_errors, [
							"file" => "section.csv",
							"line" => $sectionLineCount,
							"message" => $current_error
						]);
					}
				}

				fclose($section);
				@unlink($section_path);
				#endregion

				#region prerequisite Table
				$prerequisiteDAO->removeAll();
				$prerequisiteLineCount = 1;
				
				$header = fgetcsv($prerequisite);

				while (($data = fgetcsv($prerequisite)) != false){

					$prerequisiteLineCount++;

					#region validation of course data
					$current_error = array();
					
					$isEmptyLine = checkEmptyLine($data, $header);

					$data_course = trim($data[0]);
					$data_prerequisite = trim($data[1]);

					if(!empty($isEmptyLine)){
						$current_error = $isEmptyLine;
					} else {
						if($courseDAO->getCourse($data_course) == null){
							array_push($current_error, "invalid course");
						}
						if($courseDAO->getCourse($data[1]) == null){
							array_push($current_error, "invalid prerequisite");
						}
					}
					#endregion

					if(empty($current_error)){
						$prerequisiteDAO->addPrerequisite(new Prerequisite($data_course, $data_prerequisite));
						$prerequisite_processed++;
					}else{
						array_push($prerequisite_errors, [
							"file" => "prerequisite.csv",
							"line" => $prerequisiteLineCount,
							"message" => $current_error
						]);
					}
				}
				
				fclose($prerequisite);
				@unlink($prerequisite_path);
				#endregion

				#region course_completed Table
				$course_completedDAO->removeAll();
				$coursecompletedLineCount = 1;
				
				$header = fgetcsv($course_completed);

				$temp_course_completed_arr = array();
				$temp_course_completed_dic = array();

				while (($data = fgetcsv($course_completed)) != false) {
					$data_userid = trim($data[0]);
					$data_code = trim($data[1]);
					array_push($temp_course_completed_arr, [$data_userid, $data_code]);
					if (array_key_exists($data_userid, $temp_course_completed_dic)) {
						array_push($temp_course_completed_dic[$data_userid], $data_code);
					} else {
						$temp_course_completed_dic[$data_userid] = array($data_code);
					}
				}

				foreach($temp_course_completed_arr as $data){

					$coursecompletedLineCount++;

					#region validation of course data
					$current_error = array();
					
					$isEmptyLine = checkEmptyLine($data, $header);

					$data_userid = trim($data[0]);
					$data_code = trim($data[1]);

					if(!empty($isEmptyLine)){
						$current_error = $isEmptyLine;
					} else {
						if($studentDAO->getStudent($data_userid) == null){
							array_push($current_error, "invalid userid");
						}
						if($courseDAO->getCourse($data_code) == null){
							array_push($current_error, "invalid course");
						}
	
						// get the list of prerequisites (e.g. IS104 requires IS103 and IS103 requires IS102.)
						// IS103 AND IS102 will be in the list
						$currentPrerequisite = $prerequisiteDAO->getPrerequisite($data_code);
						$allPrerequisite = [];
						while($currentPrerequisite != null){
							if(!in_array($currentPrerequisite[0]->prerequisite, $allPrerequisite)){
								array_push($allPrerequisite, $currentPrerequisite[0]->prerequisite);
							}
							$currentPrerequisite = array_merge($currentPrerequisite, $prerequisiteDAO->getPrerequisite($currentPrerequisite[0]->prerequisite));
							array_splice($currentPrerequisite, 0, 1);
						}
	
						// check if the list of prerequisite is completed
						$checkCourseAttempted = False;
						foreach($allPrerequisite as $prerequisite){
							if(!in_array($prerequisite, $temp_course_completed_dic[$data_userid])){
								$checkCourseAttempted = True;
							}
						}
						if($checkCourseAttempted){
							array_push($current_error, "invalid course completed");
						}
					}
					#endregion

					if(empty($current_error)){
						$course_completedDAO->addCourseCompleted(new CourseCompleted($data_userid, $data_code));
						$course_completed_processed++;
					}else{
						array_push($course_completed_errors, [
							"file" => "course_completed.csv",
							"line" => $coursecompletedLineCount,
							"message" => $current_error
						]);
					}
				}
				
				fclose($course_completed);
				@unlink($course_completed_path);
				#endregion

				#region bid Table
				$bidDAO->removeAll();
				$bidLineCount = 1;

				$header = fgetcsv($bid);

				while (($data = fgetcsv($bid)) != false){

					$bidLineCount++;

					#region validation of course data
					$current_error = array();

					$duplicateBid = False;
					
					$isEmptyLine = checkEmptyLine($data, $header);

					$data_userid = trim($data[0]);
					$data_amount = trim($data[1]);
					$data_code = trim($data[2]);
					$data_section = trim($data[3]);

					if(!empty($isEmptyLine)){
						$current_error = $isEmptyLine;
					} else {
						if($studentDAO->getStudent($data_userid) == null){
							array_push($current_error, "invalid userid");
						}
						if(!is_numeric($data_amount) || $data_amount < 10.0 || preg_match('/\.\d{3,}/', $data_amount)){
							array_push($current_error, "invalid amount");
						}
						if($courseDAO->getCourse($data_code) == null){
							array_push($current_error, "invalid course");
						}else{
							if($sectionDAO->getSection($data_code, $data_section) == null){
								array_push($current_error, "invalid section");
							}
						}
						if(empty($current_error)){
							$duplicateBid = ($bidDAO->checkBidforDelete($data_userid, $data_code, $data_section, 1) != null);
							if($bidding_roundDAO->activeRound()->roundNo == "1"){
								$student = $studentDAO->getStudent($data_userid);
								$course = $courseDAO->getCourse($data_code);
								if($student->school != $course->school){
									array_push($current_error, "not own school course");
								}
							}
							$allEnrolled = $enrolledDAO->getEnrolledFromUser($data_userid);
							// check for all the successful bids of a certain user
							$allUserBids = $bidDAO->getBidFromUser($data_userid);
		
							$currentBidTiming = $sectionDAO->getSection($data_code, $data_section);
							$currentStart = DateTime::createFromFormat('H:i:s', $currentBidTiming->start);
							$currentEnd = DateTime::createFromFormat('H:i:s', $currentBidTiming->end);
		
							$currentBidExamTiming = $courseDAO->getCourse($data_code);
							$currentExamStart = DateTime::createFromFormat('H:i:s', $currentBidExamTiming->examStart);
							$currentExamEnd = DateTime::createFromFormat('H:i:s', $currentBidExamTiming->examEnd);
							
							if(!$duplicateBid){
								foreach($allUserBids as $userBid){
									if ($data_code != $userBid->code){
										if($sectionDAO->getSection($userBid->code, $userBid->section)->day == $currentBidTiming->day){
											$bidStart = DateTime::createFromFormat('H:i:s', $sectionDAO->getSection($userBid->code, $userBid->section)->start);
											$bidEnd = DateTime::createFromFormat('H:i:s', $sectionDAO->getSection($userBid->code, $userBid->section)->end);
											if(($bidStart >= $currentStart && $bidStart < $currentEnd) || ($bidEnd > $currentStart && $bidEnd <= $currentEnd) || ($currentStart >= $bidStart && $currentStart < $bidEnd) || ($currentEnd > $bidStart && $currentEnd <= $bidEnd)){
												array_push($current_error, "class timetable clash");
												break;
											}
										}
									}
								}
								if(!in_array("class timetable clash", $current_error)){
									foreach($allEnrolled as $enrolled){
										if ($data_code != $enrolled->code){
											if($sectionDAO->getSection($enrolled->code, $enrolled->section)->day == $currentBidTiming->day){
												$enrolledStart = DateTime::createFromFormat('H:i:s', $sectionDAO->getSection($enrolled->code, $enrolled->section)->start);
												$enrolledEnd = DateTime::createFromFormat('H:i:s', $sectionDAO->getSection($enrolled->code, $enrolled->section)->end);
												if(($enrolledStart >= $currentStart && $enrolledStart < $currentEnd) || ($enrolledEnd > $currentStart && $enrolledEnd <= $currentEnd) || ($currentStart >= $enrolledStart && $currentStart < $enrolledEnd) || ($currentEnd > $enrolledStart && $currentEnd <= $enrolledEnd)){
													array_push($current_error, "class timetable clash");
													break;
												}
											}
										}
									}
								}
								foreach($allUserBids as $userBid){
									if ($data_code != $userBid->code){
										if($courseDAO->getCourse($userBid->code)->examDate == $currentBidExamTiming->examDate){
											$bidExamStart = DateTime::createFromFormat('H:i:s', $courseDAO->getCourse($userBid->code)->examStart);
											$bidExamEnd = DateTime::createFromFormat('H:i:s', $courseDAO->getCourse($userBid->code)->examEnd);
											if(($bidExamStart >= $currentExamStart && $bidExamStart < $currentExamEnd) || ($bidExamEnd > $currentExamStart && $bidExamEnd <= $currentExamEnd) || ($currentExamStart >= $bidExamStart && $currentExamStart < $bidExamEnd) || ($currentExamEnd > $bidExamStart && $currentExamEnd <= $bidExamEnd)){
												array_push($current_error, "exam timetable clash");
												break;
											}	
										}
									}
								}
								if(!in_array("exam timetable clash", $current_error)){
									foreach($allEnrolled as $enrolled){
										if ($data_code != $enrolled->code){
											if($courseDAO->getCourse($enrolled->code)->examDate == $currentBidExamTiming->examDate){
												$enrolledExamStart = DateTime::createFromFormat('H:i:s', $courseDAO->getCourse($enrolled->code)->examStart);
												$enrolledExamEnd = DateTime::createFromFormat('H:i:s', $courseDAO->getCourse($enrolled->code)->examEnd);
												if(($enrolledStart >= $currentExamStart && $enrolledStart < $currentExamEnd) || ($enrolledEnd > $currentExamStart && $enrolledEnd <= $currentExamEnd) || ($currentExamStart >= $enrolledStart && $currentExamStart < $enrolledEnd) || ($currentExamEnd > $enrolledStart && $currentExamEnd <= $enrolledEnd)){
													array_push($current_error, "exam timetable clash");
													break;
												}
											}
										}
									}
								}
							}
							$allPrerequisite = $prerequisiteDAO->getPrerequisite($data_code);
							$allCourseCompleted = $course_completedDAO->getCourseCompleted($data_userid);
							if(!empty($allPrerequisite)){
								foreach($allPrerequisite as $prerequisite){
									$checkCourseAttempted = False;
									foreach($allCourseCompleted as $courseCompleted){
										if($prerequisite->prerequisite == $courseCompleted->code){
											$checkCourseAttempted = True;
										}
									}
								}
								if(!$checkCourseAttempted){
									array_push($current_error, "incomplete prerequisites");
								}
							}
							foreach($allCourseCompleted as $courseCompleted){
								if($data_code == $courseCompleted->code){
									array_push($current_error, "course completed");
								}
							}
							if(!$duplicateBid && $bidDAO->checkBid($data_userid, $data_code, 1) == null){
								if(count($allUserBids) >= 5){
									array_push($current_error, "section limit reached");
								}
								if(is_numeric($data_amount) && $data_amount > $studentDAO->getStudent($data_userid)->edollar){
									array_push($current_error, "not enough e-dollar");
								}
							} else {
								$userBid = $bidDAO->checkBid($data_userid, $data_code, 1);
								if(is_numeric($data_amount) && $data_amount > $studentDAO->getStudent($data_userid)->edollar + $userBid->amount){
									array_push($current_error, "not enough e-dollar");
								}
							}
						}
					}
					#endregion

					if(empty($current_error)){
						if(!$duplicateBid && $bidDAO->checkBid($data_userid, $data_code, 1) == null){
							$bidDAO->addBid(new Bid($data_userid, $data_amount, $data_code, $data_section, "pending", 1));
							$currentStudent = $studentDAO->getStudent($data_userid);
							$studentDAO->updateStudent(new Student($currentStudent->userid, $currentStudent->password, $currentStudent->name, $currentStudent->school, $currentStudent->edollar - $data_amount));
							$bid_processed++;
						} else {
							$currentStudent = $studentDAO->getStudent($data_userid);
							$decode_data = ["userid" => $data_userid,"amount" => $data_amount,"course" => $data_code,"section" => $data_section];
							$result = updateBid($decode_data);
							$bid_processed++;
						}
					} else {
						array_push($bid_errors, [
							"file" => "bid.csv",
							"line" => $bidLineCount,
							"message" => $current_error
						]);
					}
				}

				fclose($bid);
				@unlink($bid_path);
				#endregion
			}
		}
	}

	array_push($lines, [
		"bid.csv" => $bid_processed,
	]);
	array_push($lines, [
		"course.csv" => $course_processed,
	]);
	array_push($lines, [
		"course_completed.csv" => $course_completed_processed,
	]);
	array_push($lines, [
		"prerequisite.csv" => $prerequisite_processed,
	]);
	array_push($lines, [
		"section.csv" => $section_processed,
	]);
	array_push($lines, [
		"student.csv" => $student_processed,
	]);

	$errors = array_merge((array)$errors, (array)$bid_errors);
	$errors = array_merge((array)$errors, (array)$course_errors);
	$errors = array_merge((array)$errors, (array)$course_completed_errors);
	$errors = array_merge((array)$errors, (array)$prerequisite_errors);
	$errors = array_merge((array)$errors, (array)$section_errors);
	$errors = array_merge((array)$errors, (array)$student_errors);

	# Sample code for returning JSON format errors. remember this is only for the JSON API. Humans should not get JSON errors.
	if (!isEmpty($errors))
	{	
		$result = [ 
			"status" => "error",
			"num-record-loaded" => $lines,
			"error" => $errors
		];
	}

	else
	{	
		// $sortclass = new Sort();
		// $errors = $sortclass->sort_it($errors,"sort_it");
		$result = [ 
			"status" => "success",
			"num-record-loaded" => $lines
		];
	}

	if(!$json){
		$result['bidtable'] = $removeall;
		$result['isStartOK'] = $isStartOK;
	}

	return $result;

}
?>
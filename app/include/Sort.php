<?php
class Sort {
	function title($a, $b)
	{
	    return strcmp($a->title,$b->title);
	}


	function bootstrap($a, $b)
	{
		return strcmp(end($a),end($b));
	}

	function sort_bid_amount($a, $b)
	{
		// return strcmp($a->amount, $b->amount);
		if( $a->amount == $b->amount) {
			return 0; 
		}
    	return $a->amount < $b->amount ? 1 : -1;
	}

	function sort_it($list,$sorttype)
	{
		usort($list,array($this,$sorttype));
		return $list;
	}

	function common_validation($a, $b)
	{
		list($firstNameA, $lastNameA) = explode(" ", $a, 2);
		list($firstNameB, $lastNameB) = explode(" ", $b, 2);

		return strcmp($lastNameA, $lastNameB);
	}
}

?>
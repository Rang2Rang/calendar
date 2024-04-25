<?php
	include $_SERVER['DOCUMENT_ROOT'].'/function/php.php';

	session_start();
	$domain = 'http://'.$_SERVER['SERVER_NAME']; // Domain 변수
	$file_path = str_replace('.php', '' , $_SERVER['PHP_SELF'] );

	// 코어타입 상수가 선언되어있을 때 수행
	if(defined('__CORE_TYPE__') === true){
		$core_type_arr = array('view', 'api');
		// 코어타입의 유효성 검사
		if(in_array(__CORE_TYPE__, $core_type_arr) === true){

			//로그인 검사를 하지 않아도 되는 페이지
			$not_login = array();
			$not_login[] = '/yangsan';




		// 코어타입 상수가 view 일 때 수행
		if(__CORE_TYPE__ === 'view'){
			$echo_js = ''; // js script 태그 안에 한번에 출력할 문자열(string)변수
			$echo_js .= 'var domain = "'.$domain.'";'; // 도메인 변수 선언
			$echo_js .= file_get_contents($_SERVER['DOCUMENT_ROOT'].'/function/js.js'); // js.js 이어 붙이기
			echo '<script>'.$echo_js.'</script>'; // js Core 출력
			echo '<script src="'.$domain.'/lib/jquery.js"></script>'; // Jquery 출력

			// skin 호출(js, css)
			if(file_exists($_SERVER['DOCUMENT_ROOT'].'/skin/skin.js')=== true){
				$skin_js = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/skin/skin.js');
				echo '<script id="skin_js"> '.$skin_js.' </script> ';
			}
			if(file_exists($_SERVER['DOCUMENT_ROOT'].'/skin/skin.css')=== true){
				$skin_css = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/skin/skin.css');
				echo '<style id="skin_css"> '.$skin_css.' </style> ';
			}
			// page 호출(js, css)
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$file_path.'.js')=== true){
				$page_js = file_get_contents($_SERVER['DOCUMENT_ROOT'].$file_path.'.js');
				echo '<script id="page_js">'.$page_js.'</script> ';
			}
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$file_path.'.css')=== true){
				$page_css = file_get_contents($_SERVER['DOCUMENT_ROOT'].$file_path.'.css');
				echo '<style id="page_css">'.$page_css.'</style> ';
			}

			// // 메뉴바 호출
			

			// if(in_array($file_path,$not_login) === false){

			// $topbar = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/topbar.html');

			// echo $topbar;

			// }
		}
		// 코어타입 상수가 api 일 때 수행
		if(__CORE_TYPE__ === 'api'){
			$_POST = file_get_contents('php://input');
			$_POST = json_decode($_POST, true);
		}
		
	}
}



?>
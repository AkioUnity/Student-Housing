<?php

if(isset($_POST['book']) || (ENABLE_BOOKING_REQUESTS == 1 && isset($_POST['request']))){
	
	if(isset($_SESSION['book'])) unset($_SESSION['book']);
    $num_nights = $_POST['nights'];
    
    $_SESSION['book']['from_date'] = $_POST['from_time'];
    $_SESSION['book']['to_date'] = $_POST['to_time'];
    $_SESSION['book']['nights'] = $num_nights;
    $_SESSION['book']['adults'] = $_POST['adults'];
    $_SESSION['book']['children'] = $_POST['children'];
    $_SESSION['book']['extra_services'] = array();
    $_SESSION['book']['activities'] = array();
    $_SESSION['book']['rooms'] = array();
    
    $_SESSION['book']['total'] = 0;
    
    if(isset($_POST['book'])){
		
		$_SESSION['book']['adults'] = 0;
		$_SESSION['book']['children'] = 0;
        
        $_SESSION['book']['amount_rooms'] = 0;
        $_SESSION['book']['amount_activities'] = 0;
        $_SESSION['book']['amount_services'] = 0;
        
        $_SESSION['book']['duty_free_rooms'] = 0;
        $_SESSION['book']['duty_free_activities'] = 0;
        $_SESSION['book']['duty_free_services'] = 0;
       
        $_SESSION['book']['tax_rooms_amount'] = 0;
        $_SESSION['book']['tax_activities_amount'] = 0;
        $_SESSION['book']['tax_services_amount'] = 0;
        
        $_SESSION['book']['discount'] = 0;
        $_SESSION['book']['discount_type'] = '';
        $_SESSION['book']['discount_amount'] = 0;
        
        $_SESSION['book']['taxes'] = array();
							
		$_SESSION['book']['sessid'] = uniqid();
        
        $num_rooms = 0;
        $num_adults = 0;
        $num_children = 0;
        
        if(isset($_POST['amount']) && is_array($_POST['amount'])){
            foreach($_POST['amount'] as $id_room => $values){
                foreach($values as $i => $value){
                    
                    if(isset($_POST['num_adults'][$id_room][$i]) && isset($_POST['num_children'][$id_room][$i]) && isset($_POST['room_'.$id_room])){
                        
                        $room_title = $_POST['room_'.$id_room];
                        $adults = $_POST['num_adults'][$id_room][$i];
                        $children = $_POST['num_children'][$id_room][$i];
                        $duty_free = $_POST['duty_free'][$id_room][$i];
                        
                        if(is_numeric($adults) && is_numeric($children) && ($adults+$children) > 0 && $value > 0){
                            $num_adults += $adults;
                            $num_rooms++;
                            
                            $data = array();
                            $data['id'] = null;
                            $data['id_room'] = $id_room;
                            $data['from_date'] = $_POST['from_time'];
                            $data['to_date'] = $_POST['to_time'];
                            $data['add_date'] = time();
                            $data['sessid'] = $_SESSION['book']['sessid'];
                            
                            $result_room_lock = db_prepareInsert($db, 'pm_room_lock', $data);
                            $result_room_lock->execute();
                            
                            $_SESSION['book']['rooms'][$id_room][$i]['title'] = $room_title;
                            $_SESSION['book']['rooms'][$id_room][$i]['adults'] = $adults;
                            $_SESSION['book']['rooms'][$id_room][$i]['children'] = $children;
                            $_SESSION['book']['rooms'][$id_room][$i]['amount'] = $value;
                            $_SESSION['book']['rooms'][$id_room][$i]['duty_free'] = $duty_free;
                            $_SESSION['book']['rooms'][$id_room][$i]['tax_rate'] = 0;
                            $_SESSION['book']['rooms'][$id_room][$i]['child_age'] = array();
                            
                            if(isset($_POST['child_age'][$id_room][$i])){
                                foreach($_POST['child_age'][$id_room][$i] as $age){
                                    if($age != '')
                                        $_SESSION['book']['rooms'][$id_room][$i]['child_age'][] = $age;
                                }
                                $children = count($_SESSION['book']['rooms'][$id_room][$i]['child_age']);
                            }
                            $num_children += $children;
                            $_SESSION['book']['rooms'][$id_room][$i]['children'] = $children;
                            
                            $_SESSION['book']['adults'] += $adults;
                            $_SESSION['book']['children'] += $children;
                            
                            $_SESSION['book']['taxes'] = array();
                            
                            if(isset($_POST['taxes'][$id_room][$i])){
                                $taxes = $_POST['taxes'][$id_room][$i];
                                if(is_array($taxes)){
									$_SESSION['book']['rooms'][$id_room][$i]['tax_rate'] = number_format((reset($taxes)/$duty_free), 2, '.', '')*100;
                                    foreach($taxes as $tax_id => $tax_amount){
                                        $_SESSION['book']['tax_rooms_amount'] += $tax_amount;
                                        if(!isset($_SESSION['book']['taxes'][$tax_id]['rooms'])) $_SESSION['book']['taxes'][$tax_id]['rooms'] = 0;
                                        $_SESSION['book']['taxes'][$tax_id]['rooms'] += $tax_amount;
                                    }
                                }
                            }
                            
                            $_SESSION['book']['amount_rooms'] += $value;
                            $_SESSION['book']['duty_free_rooms'] += $duty_free;
                        }
                    }
                }
            }
            $_SESSION['book']['num_rooms'] = $num_rooms;
        }
        
        //$tourist_tax = (TOURIST_TAX_TYPE == 'fixed') ? $_SESSION['book']['adults']*$num_nights*TOURIST_TAX : $_SESSION['book']['amount_rooms']*TOURIST_TAX/100;
        
        //$_SESSION['book']['tourist_tax'] = (ENABLE_TOURIST_TAX == 1) ? $tourist_tax : 0;
        
        $_SESSION['book']['total'] = $_SESSION['book']['duty_free_rooms']+$_SESSION['book']['tax_rooms_amount']/*+$_SESSION['book']['tourist_tax']*/;
        $_SESSION['book']['down_payment'] = (ENABLE_DOWN_PAYMENT == 1 && DOWN_PAYMENT_RATE > 0 && $_SESSION['book']['total'] >= DOWN_PAYMENT_AMOUNT) ? $_SESSION['book']['total']*DOWN_PAYMENT_RATE/100 : 0;
    }
    
    if(isset($_SESSION['book']['id'])) unset($_SESSION['book']['id']);
    
    $result_activity = $db->query('SELECT * FROM pm_activity WHERE checked = 1 AND lang = '.LANG_ID);
    if(isset($_SESSION['book']['activities'])) unset($_SESSION['book']['activities']);
    
    if($result_activity !== false && $db->last_row_count() > 0){
        $_SESSION['book']['activities'] = array();
        header('Location: '.DOCBASE.$sys_pages['booking-activities']['alias']);
    }else
        header('Location: '.DOCBASE.$sys_pages['details']['alias']);
    
    exit();
}

$field_notice = array();
$msg_error = '';
$msg_success = '';
$room_stock = 1;
$max_people = 30;
$search_limit = 8;
$search_offset = (isset($_GET['offset']) && is_numeric($_GET['offset'])) ? $_GET['offset'] : 0;

/*********** Num adults ************/
if(isset($_POST['num_adults']) && is_numeric($_POST['num_adults'])) $_SESSION['num_adults'] = $_POST['num_adults'];
elseif(isset($_GET['num_adults']) && is_numeric($_GET['num_adults'])) $_SESSION['num_adults'] = $_GET['num_adults'];
elseif(isset($_SESSION['book']['adults'])) $_SESSION['num_adults'] = $_SESSION['book']['adults'];
elseif(!isset($_SESSION['num_adults'])) $_SESSION['num_adults'] = 1;

/********** Num children ***********/
if(isset($_POST['num_children']) && is_numeric($_POST['num_children'])) $_SESSION['num_children'] = $_POST['num_children'];
elseif(isset($_GET['num_children']) && is_numeric($_GET['num_children'])) $_SESSION['num_children'] = $_GET['num_children'];
elseif(isset($_SESSION['book']['children'])) $_SESSION['num_children'] = $_SESSION['book']['children'];
elseif(!isset($_SESSION['num_children'])) $_SESSION['num_children'] = 0;

/****** Check in / out date ********/
if(isset($_SESSION['book']['from_date'])) $from_time = $_SESSION['book']['from_date'];
else $from_time = gmtime();

if(isset($_SESSION['book']['to_date'])) $to_time = $_SESSION['book']['to_date'];
else $to_time = gmtime()+86400;

if(isset($_POST['from_date']) && !empty($_POST['from_date'])) $_SESSION['from_date'] = htmlentities($_POST['from_date'], ENT_QUOTES, 'UTF-8');
elseif(isset($_GET['from'])) $_SESSION['from_date'] = gmdate('d/m/Y', gm_strtotime(htmlentities($_GET['from'], ENT_QUOTES, 'UTF-8')));
elseif(!isset($_SESSION['from_date'])) $_SESSION['from_date'] = gmdate('d/m/Y', $from_time);

if(isset($_POST['to_date']) && !empty($_POST['to_date'])) $_SESSION['to_date'] = htmlentities($_POST['to_date'], ENT_QUOTES, 'UTF-8');
elseif(isset($_GET['to'])) $_SESSION['to_date'] = gmdate('d/m/Y', gm_strtotime(htmlentities($_GET['to'], ENT_QUOTES, 'UTF-8')));
elseif(!isset($_SESSION['to_date'])) $_SESSION['to_date'] = gmdate('d/m/Y', $to_time);

/********** Searched room **********/
if(isset($_POST['room_id']) && is_numeric($_POST['room_id'])) $_SESSION['room_id'] = $_POST['room_id'];
elseif(isset($_SESSION['room_id']) && is_numeric($_SESSION['room_id'])) $_SESSION['room_id'] = $_SESSION['room_id'];
elseif(!isset($_SESSION['room_id'])) $_SESSION['room_id'] = 0;

$num_people = $_SESSION['num_adults']+$_SESSION['num_children'];

if(!is_numeric($_SESSION['num_adults'])) $field_notice['num_adults'] = $texts['REQUIRED_FIELD'];
if(!is_numeric($_SESSION['num_children'])) $field_notice['num_children'] = $texts['REQUIRED_FIELD'];

if($_SESSION['from_date'] == '') $field_notice['from_date'] = $texts['REQUIRED_FIELD'];
else{
    $time = explode('/', $_SESSION['from_date']);
    if(count($time) == 3) $time = gm_strtotime($time[2].'-'.$time[1].'-'.$time[0].' 00:00:00');
    if(!is_numeric($time)) $field_notice['from_date'] = $texts['REQUIRED_FIELD'];
    else $from_time = $time;
}
if($_SESSION['to_date'] == '') $field_notice['to_date'] = $texts['REQUIRED_FIELD'];
else{
    $time = explode('/', $_SESSION['to_date']);
    if(count($time) == 3) $time = gm_strtotime($time[2].'-'.$time[1].'-'.$time[0].' 00:00:00');
    if(!is_numeric($time)) $field_notice['to_date'] = $texts['REQUIRED_FIELD'];
    else $to_time = $time;
}

$today = gm_strtotime(gmdate('Y').'-'.gmdate('n').'-'.gmdate('j').' 00:00:00');

if($from_time < $today || $to_time < $today || $to_time <= $from_time){
    $from_time = $today;
    $to_time = $today+86400;
    $_SESSION['from_date'] = gmdate('d/m/Y', $from_time);
    $_SESSION['to_date'] = gmdate('d/m/Y', $to_time);
}

if(is_numeric($from_time) && is_numeric($to_time)){
    $num_nights = ($to_time-$from_time)/86400;
}else
    $num_nights = 0;

$res_room = array();
$booked_rooms = array();
if(count($field_notice) == 0){

    if($num_nights <= 0) $msg_error .= $texts['NO_AVAILABILITY'];
    else{
        require_once(getFromTemplate('common/functions.php', false));
        $res_room = getRoomsResult($from_time, $to_time, $_SESSION['num_adults'], $_SESSION['num_children']);
        $booked_rooms = getBookedRooms($from_time, $to_time);
        if(empty($res_room)) $msg_error .= $texts['NO_AVAILABILITY'];
    }
}

$id_facility = 0;
$result_facility_file = $db->prepare('SELECT * FROM pm_facility_file WHERE id_item = :id_facility AND checked = 1 AND lang = '.DEFAULT_LANG.' AND type = \'image\' AND file != \'\' ORDER BY rank LIMIT 1');
$result_facility_file->bindParam(':id_facility', $id_facility);

$room_facilities = '0';
$result_facility = $db->prepare('SELECT * FROM pm_facility WHERE lang = '.LANG_ID.' AND FIND_IN_SET(id, :room_facilities) ORDER BY rank LIMIT 8');
$result_facility->bindParam(':room_facilities', $room_facilities);

$id_room = 0;
$result_rate = $db->prepare('SELECT MIN(price) as price FROM pm_rate WHERE id_room = :id_room');
$result_rate->bindParam(':id_room', $id_room);

$result_room_file = $db->prepare('SELECT * FROM pm_room_file WHERE id_item = :id_room AND checked = 1 AND lang = '.LANG_ID.' AND type = \'image\' AND file != \'\' ORDER BY rank LIMIT 1');
$result_room_file->bindParam(':id_room', $id_room, PDO::PARAM_STR);

$query_room = 'SELECT * FROM pm_room WHERE checked = 1 AND lang = '.LANG_ID.' ORDER BY';
if($_SESSION['room_id'] != 0) $query_room .= ' CASE WHEN id = '.$_SESSION['room_id'].' THEN 1 ELSE 2 END,';
if(!empty($res_room)) $query_room .= ' CASE WHEN id IN('.implode(',', array_keys($res_room)).') THEN 1 ELSE 2 END,';
if(!empty($booked_rooms)) $query_room .= ' CASE WHEN id IN('.implode(',', array_keys($booked_rooms)).') THEN 2 ELSE 1 END,';
$query_room .= ' CASE WHEN (max_people-'.$num_people.') >= 0 THEN (max_people-'.$num_people.') ELSE 100 END, 
                CASE WHEN (max_people-'.$num_people.') < 0 THEN ('.$num_people.'-max_people) ELSE 100 END, 
                rank';

$num_results = 0;
$result_room = $db->query($query_room);
if($result_room !== false) $num_results = $db->last_row_count();

$query_room .= ' LIMIT '.$search_limit.' OFFSET '.$search_offset;

$result_room = $db->query($query_room);

if(isset($_GET['action'])){
	if(isset($_SESSION['book'])) unset($_SESSION['book']);
    if($_GET['action'] == 'confirm')
        $msg_success .= $texts['PAYMENT_SUCCESS_NOTICE'];
    elseif($_GET['action'] == 'cancel')
        $msg_error .= $texts['PAYMENT_CANCEL_NOTICE'];
}

/* ==============================================
 * CSS AND JAVASCRIPT USED IN THIS MODEL
 * ==============================================
 */
$javascripts[] = DOCBASE.'js/plugins/jquery.event.calendar/js/jquery.event.calendar.js';

if(is_file(SYSBASE.'js/plugins/jquery.event.calendar/js/languages/jquery.event.calendar.'.LANG_TAG.'.js'))
    $javascripts[] = DOCBASE.'js/plugins/jquery.event.calendar/js/languages/jquery.event.calendar.'.LANG_TAG.'.js';
else
    $javascripts[] = DOCBASE.'js/plugins/jquery.event.calendar/js/languages/jquery.event.calendar.en.js';
    
$stylesheets[] = array('file' => DOCBASE.'js/plugins/jquery.event.calendar/css/jquery.event.calendar.css', 'media' => 'all');

require(getFromTemplate('common/header.php', false)); ?>

<section id="page">
    
    <?php include(getFromTemplate('common/page_header.php', false)); ?>
    
    <div id="content" class="pt30 pb30">
        
        <div id="search-page" class="mb20">
            <div class="container">
                <?php include(getFromTemplate('common/search.php', false)); ?>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <div class="container">
            <div class="alert alert-success text-center lead" style="display:none;"></div>
            <div class="alert alert-danger text-center lead" style="display:none;"></div>
        </div>
        
        <form action="<?php echo DOCBASE.$sys_pages['booking']['alias']; ?>" method="post" class="ajax-form">
            <input type="hidden" name="from_time" value="<?php echo $from_time; ?>">
            <input type="hidden" name="to_time" value="<?php echo $to_time; ?>">
            <input type="hidden" name="nights" value="<?php echo $num_nights; ?>">
            
            <div class="container mb20" id="booking-summary">
                <div class="row">
                    <div class="col-md-6">
                        <?php echo $texts['BOOKING_NOTICE']; ?>
                    </div>
                    <div class="col-md-6 text-center">
                        <p class="lead mb0"><?php echo $texts['CHECK_IN'].' <big><b>'.$_SESSION['from_date'].'</b></big> <big><i class="fas fa-fw fa-arrow-right"></i></big> '.$texts['CHECK_OUT'].' <big><b>'.$_SESSION['to_date'].'</b></big>'; ?></p>
                        <span id="booking-amount">
							<?php
							$room_stock = 0;
							if($result_room !== false){
								foreach($result_room as $row){
									$id_room = $row['id'];
									$room_stock += isset($res_room[$id_room]['room_stock']) ? $res_room[$id_room]['room_stock'] : $row['stock'];
								}
							}
							$result_room->execute();
							
							if(ENABLE_BOOKING_REQUESTS == 1 && ($num_nights <= 0 || (empty($res_room) && $room_stock > 0) || (!empty($res_room) && $room_stock <= 0))){
								echo '
								<input type="hidden" name="adults" value="'.$_SESSION['num_adults'].'">
								<input type="hidden" name="children" value="'.$_SESSION['num_children'].'">
								<button name="request" class="btn btn-default btn-lg btn-block mt5"><i class="fas fa-fw fa-comment"></i> '.$texts['MAKE_A_REQUEST'].'</small></button>';
							} ?>
						</span>
                    </div>
                </div>
            </div>
            
            <?php
            if($page['text'] != ''){ ?>
                <div class="container mb20"><?php echo $page['text']; ?></div>
                <?php
            } ?>
            
            <div class="container">
                <?php
                if($result_room !== false){
                    foreach($result_room as $i => $row){
                        $id_room = $row['id'];
                        $room_title = $row['title'];
                        $room_alias = $row['alias'];
                        $room_subtitle = $row['subtitle'];
                        $room_descr = $row['descr'];
                        $room_price = $row['price'];
                        $max_adults = $row['max_adults'];
                        $max_children = $row['max_children'];
                        $max_people = $row['max_people'];
                        $min_people = $row['min_people'];
                        $room_facilities = $row['facilities'];
                        
                        $room_stock = isset($res_room[$id_room]['room_stock']) ? $res_room[$id_room]['room_stock'] : $row['stock'];
                        
                        $min_price = $room_price;
                        $result_rate->execute();
                        if($result_rate !== false && $db->last_row_count() > 0){
                            $row = $result_rate->fetch();
                            $price = $row['price'];
                            if($price > 0) $min_price = $price;
                        }
                        $type = $texts['NIGHT'];
                        if(!isset($res_room[$id_room]) || isset($res_room[$id_room]['error'])){
                            $amount = $min_price;
                            $full_price = 0;
                        }else{
                            $amount = $res_room[$id_room]['amount']+$res_room[$id_room]['fixed_sup'];
                            $full_price = $res_room[$id_room]['full_price']+$res_room[$id_room]['fixed_sup'];
                            $type = $num_nights.' '.getAltText($texts['NIGHT'], $texts['NIGHTS'], $num_nights);
                        } ?>
                        
                        <input type="hidden" name="rooms[]" value="<?php echo $id_room; ?>">
                        <input type="hidden" name="room_<?php echo $id_room; ?>" value="<?php echo $room_title; ?>">
                        
						<div class="boxed mb20 booking-result form-<?php echo $i; ?>">
                            <div class="row">
                                <div class="col-md-3">
                                    <?php
                                    $result_room_file->execute();
                                    if($result_room_file !== false && $db->last_row_count() > 0){
                                        $row = $result_room_file->fetch(PDO::FETCH_ASSOC);

                                        $file_id = $row['id'];
                                        $filename = $row['file'];
                                        $label = $row['label'];

                                        $realpath = SYSBASE.'medias/room/small/'.$file_id.'/'.$filename;
                                        $thumbpath = DOCBASE.'medias/room/small/'.$file_id.'/'.$filename;
                                        $zoompath = DOCBASE.'medias/room/big/'.$file_id.'/'.$filename;

                                        if(is_file($realpath)){ ?>
                                            <div class="img-container md">
                                                <img alt="<?php echo $label; ?>" src="<?php echo $thumbpath; ?>" itemprop="photo">
                                            </div>
                                            <?php
                                        }
                                    } ?>
                                </div>
                                <div class="col-lg-4 col-md-3 col-sm-4 pt15">
                                    <h3><?php echo $room_title; ?></h3>
                                    <h4><?php echo $room_subtitle; ?></h4>
                                    <?php echo strtrunc(strip_tags($room_descr), 120); ?>
                                    <div class="clearfix mt10">
                                        <?php
                                        $result_facility->execute();
                                        if($result_facility !== false && $db->last_row_count() > 0){
                                            foreach($result_facility as $row){
                                                $id_facility = $row['id'];
                                                $facility_name = $row['name'];
                                                
                                                $result_facility_file->execute();
                                                if($result_facility_file !== false && $db->last_row_count() > 0){
                                                    $row = $result_facility_file->fetch();
                                                    
                                                    $file_id = $row['id'];
                                                    $filename = $row['file'];
                                                    $label = $row['label'];
                                                    
                                                    $realpath = SYSBASE.'medias/facility/big/'.$file_id.'/'.$filename;
                                                    $thumbpath = DOCBASE.'medias/facility/big/'.$file_id.'/'.$filename;
                                                        
                                                    if(is_file($realpath)){ ?>
                                                        <span class="facility-icon">
                                                            <img alt="<?php echo $facility_name; ?>" title="<?php echo $facility_name; ?>" src="<?php echo $thumbpath; ?>" class="tips">
                                                        </span>
                                                        <?php
                                                    }
                                                }
                                            } ?>
                                            <span class="facility-icon">
                                                <a href="<?php echo DOCBASE.$pages[9]['alias'].'/'.text_format($room_alias); ?>" title="<?php echo $texts['READMORE']; ?>" class="tips">...</a>
                                            </span>
                                            <?php
                                        } ?>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3 text-center sep pt15">
                                    <div class="price">
                                        <span itemprop="priceRange"><?php echo formatPrice($amount*CURRENCY_RATE); ?></span>
                                        <?php
                                        if($full_price > 0 && $full_price > $amount){ ?>
                                            <br><s class="text-warning"><?php echo formatPrice($full_price*CURRENCY_RATE); ?></s>
                                            <?php
                                        } ?>
                                    </div>
                                    <div class="mb10 text-muted"><?php echo $texts['PRICE']; ?> / <?php echo $type; ?></div>
                                    <?php echo $texts['CAPACITY']; ?> : <i class="fas fa-fw fa-male"></i>x<?php echo $max_people; ?>
                                    
                                    <?php
                                    if($room_stock > 0){ ?>
                                        <div class="input-group input-group-sm mt10">
                                            <div class="input-group-addon"><i class="fas fa-fw fa-tags"></i> <?php echo $texts['NUM_ROOMS']; ?></div>
                                            <select name="num_rooms[<?php echo $id_room; ?>]" class="form-control sendAjaxForm selectpicker" data-target="#room-options-<?php echo $id_room; ?>" data-extratarget="#booking-amount" data-action="<?php echo getFromTemplate('common/change_num_rooms.php'); ?>?room=<?php echo $id_room; ?>">
                                                <?php
                                                for($i = 0; $i <= $room_stock; $i++){ ?>
                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                    <?php
                                                } ?>
                                            </select>
                                        </div>
                                        <?php
                                    }else{ ?>
                                        <div class="mt10 btn btn-danger btn-block" disabled="disabled"><?php echo $texts['NO_AVAILABILITY']; ?></div>
                                        <?php
                                    } ?>
                                    
                                    <p class="lead mb0">
                                        <span class="clearfix"></span>
                                        <a class="btn btn-primary mt10 btn-block" href="<?php echo DOCBASE.$pages[9]['alias'].'/'.text_format($room_alias); ?>">
                                            <i class="fas fa-fw fa-plus-circle"></i>
                                            <?php echo $texts['READMORE']; ?>
                                        </a>
                                    </p>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-5 sep pt15">
                                    <div class="hb-calendar" data-cur_month="<?php echo gmdate('n', $from_time); ?>" data-cur_year="<?php echo gmdate('Y', $from_time); ?>" data-custom_var="room=<?php echo $id_room; ?>" data-day_loader="<?php echo getFromTemplate('common/get_days.php'); ?>"></div>
                                </div>
                            </div>
                        
                            <div id="room-options-<?php echo $id_room; ?>" class="room-options clearfix"></div>
                        </div>
                        <?php
                    }
                    if($search_limit > 0){
                        $nb_pages = ceil($num_results/$search_limit);
                        if($nb_pages > 1){ ?>
                            <div class="container text-center">
                                <div class="btn-group">
                                    <?php
                                    for($i = 1; $i <= $nb_pages; $i++){
                                        $offset = ($i-1)*$search_limit;
                                        
                                        if($offset == $search_offset)
                                            echo '<span class="btn btn-default disabled">'.$i.'</span>';
                                        else{
                                            $request = ($offset == 0) ? '' : '?offset='.$offset;
                                            echo '<a class="btn btn-default" href="'.DOCBASE.$sys_pages['booking']['alias'].$request.'">'.$i.'</a>';
                                        }
                                    } ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                } ?>
            </div>
        </form>
    </div>
</section>
<script>
	$(function(){
		
		$('select[name^="num_rooms"]').on('change', function(){
			var obj = $(this);
			setTimeout(function(){
				if(obj.val() > 0){
					var attr = obj.attr('name').match(/\[(\d+)\]/);
					var target = $('select[name^="num_adults['+attr[1]+']');
					target.val('1').trigger('change');
				}
			}, 500);
		});
		$('.room-options').on('change', '[name^="num_children"]', function(){
			var attr = $(this).attr('name').match(/\[(\d+)\]\[(\d+)\]/);
			var target = $('#children-options-'+attr[1]+'-'+attr[2]);
			var num = $(this).val();
			var html = '<?php echo $texts['CHILDREN_AGE']; ?>:<br>';
			for(var i = 0; i < num; i++){
				html +=
				'<div class="input-group input-group-sm">'+
					'<div class="input-group-addon"><?php echo ucfirst($texts['CHILD']); ?> '+(i+1)+'</div>'+
						'<select name="child_age['+attr[1]+']['+attr[2]+']['+i+']" class="form-control sendAjaxForm selectpicker" data-extratarget="#booking-amount" data-action="<?php echo getFromTemplate('common/change_num_people.php'); ?>?index='+attr[2]+'&id_room='+attr[1]+'" data-target="#room-result-'+attr[1]+'-'+attr[2]+'" style="display: none;">'+
							'<option value="">-</option>';
							for(var j = 0; j < 18; j++) html += '<option value="'+j+'">'+j+'</option>';
							html +=
						'</select>'+
					'</div>'+
				'</div>';
			}
			target.html(html);
			$('.selectpicker').selectpicker('refresh');
		});
		
		<?php
		if(isset($_POST['room_id']) && is_numeric($_POST['room_id'])){ ?>
			setTimeout(function(){
				$('select[name="num_rooms[<?php echo $_POST['room_id']; ?>]"').val('1').trigger('change');
			}, 2000);
			<?php
		} ?>
	});
</script>

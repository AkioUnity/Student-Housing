<?php
/**
 * Script called (Ajax) on scroll or click
 * loads more content with Lazy Loader
 */
$html = '';
if(!isset($lz_offset)) $lz_offset = 1;
if(!isset($lz_limit)) $lz_limit = 30;
if(isset($_POST['ajax']) && $_POST['ajax'] == 1){
    
    require_once('../../../common/lib.php');
    require_once('../../../common/define.php');

    if(isset($_POST['offset']) && is_numeric($_POST['offset'])
    && isset($_POST['limit']) && is_numeric($_POST['limit'])
    && isset($_POST['page_id']) && is_numeric($_POST['page_id'])
    && isset($_POST['page_alias'])){
        $page_id = $_POST['page_id'];
        $lz_offset = $_POST['offset'];
        $lz_limit =	$_POST['limit'];
        $page_alias = $_POST['page_alias'];
    }
}
if(isset($db) && $db !== false){
    
    if(isset($page_id) && isset($pages[$page_id]['alias'])) $page_alias = $pages[$page_id]['alias'];

    $result_room = $db->query('SELECT * FROM pm_room WHERE lang = '.LANG_ID.' AND checked = 1 ORDER BY rank LIMIT '.($lz_offset-1)*$lz_limit.', '.$lz_limit);

    $room_id = 0;

    $result_room_file = $db->prepare('SELECT * FROM pm_room_file WHERE id_item = :room_id AND checked = 1 AND lang = '.LANG_ID.' AND type = \'image\' AND file != \'\' ORDER BY rank LIMIT 1');
    $result_room_file->bindParam(':room_id', $room_id);

    $result_rate = $db->prepare('SELECT MIN(price) as price FROM pm_rate WHERE id_room = :id_room');
    $result_rate->bindParam(':id_room', $room_id);

    foreach($result_room as $i => $row){
                                
        $room_id = $row['id'];
        $room_title = $row['title'];
        $room_subtitle = $row['subtitle'];
        $room_price = $row['price'];
        $room_alias = $row['alias'];
        
        $room_alias = DOCBASE.$page_alias.'/'.text_format($room_alias);
        
        $html .= '
        <article class="col-sm-4 isotopeItem" itemscope itemtype="http://schema.org/LodgingBusiness">
            <div class="isotopeInner">
                <a itemprop="url" href="'.$room_alias.'">';
                    
                    if($result_room_file->execute() !== false && $db->last_row_count() > 0){
                        $row = $result_room_file->fetch(PDO::FETCH_ASSOC);
                        
                        $file_id = $row['id'];
                        $filename = $row['file'];
                        $label = $row['label'];
                        
                        $realpath = SYSBASE.'medias/room/medium/'.$file_id.'/'.$filename;
                        $thumbpath = DOCBASE.'medias/room/medium/'.$file_id.'/'.$filename;
                        $zoompath = DOCBASE.'medias/room/big/'.$file_id.'/'.$filename;
                        
                        if(is_file($realpath)){
                            $html .= '
                            <figure class="more-link img-container md">
                                <img alt="'.$label.'" src="'.$thumbpath.'">
                                <span class="more-action">
                                    <span class="more-icon"><i class="fa fa-link"></i></span>
                                </span>
                            </figure>';
                        }
                    }
                    $html .= '
                    <div class="isotopeContent">
                        <h3 itemprop="name">'.$room_title.'</h3>
                        <h4>'.$room_subtitle.'</h4>';
                        $min_price = $room_price;
                        if($result_rate->execute() !== false && $db->last_row_count() > 0){
                            $row = $result_rate->fetch();
                            $price = $row['price'];
                            if($price > 0) $min_price = $price;
                        }
                        $html .= '
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="price text-primary">
                                    '.$texts['FROM_PRICE'].'
                                    <span itemprop="priceRange">
                                        '.formatPrice($min_price*CURRENCY_RATE).'
                                    </span>
                                </div>
                                <div class="text-muted">'.$texts['PRICE'].' / '.$texts['NIGHT'].'</div>
                            </div>
                            <div class="col-xs-6">
                                <span class="btn btn-primary mt5 pull-right">'.$texts['MORE_DETAILS'].'</span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </a>
            </div>
        </article>';
    }
    if(isset($_POST['ajax']) && $_POST['ajax'] == 1)
        echo json_encode(array('html' => $html));
    else
        echo $html;
}

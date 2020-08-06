<?php
debug_backtrace() || die ('Direct access not permitted');

$max_adults_search = 30;
$max_children_search = 10;

if(!isset($_SESSION['num_adults']))
    $_SESSION['num_adults'] = (isset($_SESSION['book']['adults'])) ? $_SESSION['book']['adults'] : 1;
if(!isset($_SESSION['num_children']))
    $_SESSION['num_children'] = (isset($_SESSION['book']['children'])) ? $_SESSION['book']['children'] : 0;

$from_date = (isset($_SESSION['from_date'])) ? $_SESSION['from_date'] : date('j/m/Y');
$to_date = (isset($_SESSION['to_date'])) ? $_SESSION['to_date'] : date('j/m/Y', time()+86400); ?>

<form action="<?php echo DOCBASE.$sys_pages['booking']['alias']; ?>" method="post" class="booking-search">
    <?php
    if(isset($hotel_id)){ ?>
        <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
        <?php
    } ?>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                <label class="sr-only" for="from"></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fas fa-fw fa-calendar"></i> <?php echo $texts['CHECK_IN']; ?></div>
                    <input type="text" class="form-control" id="from_picker" name="from_date" value="<?php echo $from_date; ?>">
                </div>
                <div class="field-notice" rel="from_date"></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon"><i class="fas fa-fw fa-calendar"></i> <?php echo $texts['CHECK_OUT']; ?></div>
                    <input type="text" class="form-control" id="to_picker" name="to_date" value="<?php echo $to_date; ?>">
                </div>
                <div class="field-notice" rel="to_date"></div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 col-xs-6">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon"><i class="fas fa-fw fa-male"></i> <?php echo $texts['ADULTS']; ?></div>
                    <select name="num_adults" class="selectpicker form-control">
                        <?php
                        for($i = 1; $i <= $max_adults_search; $i++){
                            $select = ($_SESSION['num_adults'] == $i) ? ' selected="selected"' : '';
                            echo '<option value="'.$i.'"'.$select.'>'.$i.'</option>';
                        } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 col-xs-6">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon"><i class="fas fa-fw fa-male"></i> <?php echo $texts['CHILDREN']; ?></div>
                    <select name="num_children" class="selectpicker form-control">
                        <?php
                        for($i = 0; $i <= $max_children_search; $i++){
                            $select = ($_SESSION['num_children'] == $i) ? ' selected="selected"' : '';
                            echo '<option value="'.$i.'"'.$select.'>'.$i.'</option>';
                        } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-12 col-xs-12">
            <div class="form-group">
                <button class="btn btn-block btn-primary" type="submit" name="check_availabilities"><i class="fas fa-fw fa-search"></i> <?php echo $texts['CHECK']; ?></button>
            </div>
        </div>
    </div>
</form>

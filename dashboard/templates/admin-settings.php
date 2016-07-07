<?php
\OCP\Util::addScript('dashboard', 'settings');
\OCP\Util::addStyle('dashboard', 'settings');
?>


<div class="section" id="dashboardSettings" >

    <h2><?php p($l->t('Dashboard settings'));?></h2>
    <p><?php p($l->t('Here you can enable widgets for different groups or all users.')); ?></p>
    <div class="widgets">

        <?php
        foreach($_['availableWidgets'] as $widget) {?>
            <div class="widget">
                <h3><?php p($widget); ?></h3>
                <div class="groups">
                    <?php
                    if(in_array($widget.'-all', $_['enabledWidgetGroups'])) {
                        $enabled = 'checked=checked';
                    } else {
                        $enabled = '';
                    }
                    ?>
                    <div class="group <?php p($widget.'-all') ?>"><input type="checkbox" data-widg="<?php p($widget); ?>-all" <?php p($enabled); ?> /><?php p($l->t('all')); ?></div>
                    <?php
                    foreach( $_['groups'] as $groupId => $groupName) {
                        $id = $widget.'-'.$groupId;

                        if(in_array($id, $_['enabledWidgetGroups'])) {
                            $enabled = 'checked=checked';
                        } else {
                            $enabled = '';
                        }
                        ?>
                        <div class="group <?php p($id) ?>"><input value="1" type="checkbox" data-widg="<?php p($id) ?>" <?php p($enabled); ?> /><?php p($groupName); ?></div>
                    <?php
                    } ?>
                </div>
            </div>
        <?php
        } ?>
        <div class="widget"></div>
    </div>

</div>
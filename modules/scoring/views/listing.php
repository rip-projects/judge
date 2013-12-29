  <div class="grid-container">
        <?php if(is_array($my_score)&& count($my_score)>0):?>	
        <h1>Nilai Pribadi</h1>
        
        <table class="grid" id="bdh-grid">
            <tr>
                <th><a href="#"><?php echo l('Nama') ?></a></th>
                <th><a href="#"><?php echo l("Penilai") ?></a></th>
                <th><a href="#"><?php echo l('Manager') ?></a></th>
                <th><a href="#"><?php echo l('Tanggal') ?></a></th>
                <th><a href="#"><?php echo l('Nilai') ?></a></th>
            </tr>
            <?php foreach ($my_score as $key => $row): ?>
                <tr>
                    <td><a href="detail/<?php echo $row['id']?>"> <?php echo $row['nama'] ?></td>
                    <td><?php echo $row['penilai'] ?></td>
                    <td><?php echo $row['manager'] ?></td>
                    <td><?php echo mysql_to_human($row['judge_date']) ?></td>
                    <td><?php echo $row['average'] ?></td>
                </tr>
            <?php endforeach ?>
        </table>
        <?php endif;?>
        
        <?php if(is_array($manage_by_me)&& count($manage_by_me)>0):?>	
        <h1>Bawahan Langsung</h1>
        
        <table class="grid" id="bdh-grid">
            <tr>
                <th><a href="#"><?php echo l('Nama') ?></a></th>
                <th><a href="#"><?php echo l("Penilai") ?></a></th>
                <th><a href="#"><?php echo l('Manager') ?></a></th>
                <th><a href="#"><?php echo l('Tanggal') ?></a></th>
                <th><a href="#"><?php echo l('Nilai') ?></a></th>
            </tr>
            <?php foreach ($manage_by_me as $key => $row): ?>
                <tr>
                    <td><a href="detail/<?php echo $row['id']?>"> <?php echo $row['nama'] ?></td>
                    <td><?php echo $row['penilai'] ?></td>
                    <td><?php echo $row['manager'] ?></td>
                    <td><?php echo mysql_to_human($row['judge_date']) ?></td>
                    <td><?php echo $row['average'] ?></td>
                </tr>
            <?php endforeach ?>
        </table>
        
        <?php endif;?>
         <?php if(is_array($judge_by_me)&& count($judge_by_me)>0):?>	
        <h1>Dinilai Oleh Saya </h1>
        
        <table class="grid" id="bdh-grid">
            <tr>
                <th><a href="#"><?php echo l('Nama') ?></a></th>
                <th><a href="#"><?php echo l("Penilai") ?></a></th>
                <th><a href="#"><?php echo l('Manager') ?></a></th>
                <th><a href="#"><?php echo l('Tanggal') ?></a></th>
                <th><a href="#"><?php echo l('Nilai') ?></a></th>
            </tr>
            <?php foreach ($judge_by_me as $key => $row): ?>
                <tr>
                    <td><a href="detail/<?php echo $row['id']?>"> <?php echo $row['nama'] ?></td>
                    <td><?php echo $row['penilai'] ?></td>
                    <td><?php echo $row['manager'] ?></td>
                    <td><?php echo mysql_to_human($row['judge_date']) ?></td>
                    <td><?php echo $row['average'] ?></td>
                </tr>
            <?php endforeach ?>
        </table>
        <?php endif;?>
    </div>
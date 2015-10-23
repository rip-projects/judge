<fieldset>
    <legend>Message Detail</legend>
    <div class="clear" style="min-height: 30px;">
        <label>Sender</label>
        <span><?php echo $data['sender'] ?></span>
    </div>
    <div class="clear" style="min-height: 30px;">
        <label>Subject</label>
        <span><?php echo $data['subject'] ?></span>
    </div>
    <div class="clear" style="min-height: 30px;">
        <label>Body</label>
        <span><?php echo $data['body'] ?></span>
    </div>
</fieldset>

<div class="action-buttons">
    <a href="javascript:history.back()" class="button cancel">Back</a>
</div>
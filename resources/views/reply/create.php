<?php
if (isset($errors)) {
    foreach ($errors->all() as $error) {
        echo "<p>{$error}</p>";
    }
}
?>
<form method="POST">
    <div>
        From Name
        <input type="text" name="from_name" value="<?= $request->get('from_name') ?>">
    </div>

    <div>
        From Email
        <input type="email" name="from_email" value="<?= $request->get('from_email') ?>">
    </div>

    <div>
        To Name
        <input type="text" name="to_name" value="<?= $request->get('to_name') ?>">
    </div>

    <div>
        To Email
        <input type="email" name="to_email" value="<?= $request->get('to_email') ?>">
    </div>

    <div>
        <button type="submit">Submit</button>
    </div>
</form>
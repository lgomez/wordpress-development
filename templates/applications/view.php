<h1><?= $data['name'] ?></h1>
<table>
    <thead>
        <tr>
            <th>Key</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['package_keys'] as $index => $key) {
            ?>
            <tr>
                <td><a href="/wordpress/keys/<?= $key["id"] ?>"><?= $key["apikey"] ?></td>
                <td><?= $key["created"] ?></td>
            </tr>
            <?php
        } ?>
    </tbody>
</table>
<a href="/wordpress/applications/<?= $data['id'] ?>/delete" >Delete Application</a>

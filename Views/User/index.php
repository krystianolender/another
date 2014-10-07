<table class="table table-striped">
    <thead>
        <tr>
            <th>Nazwa użytkownika</th>
            <th>Imie</th>
            <th>Nazwisko</th>
            <th>Data Rejestracji</th>
            <th>Administrator</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo($user->username); ?></td>
                <td><?php echo($user->first_name); ?></td>
                <td><?php echo($user->last_name); ?></td>
                <td><?php echo($user->created); ?></td>
                <td><?php echo($user->is_admin) ? 'Tak' : 'Nie'; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
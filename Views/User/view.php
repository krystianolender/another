<dl>
    <dt>Identyfikator</dt>
    <dd><?php echo($user->id); ?></dd>
    <dt>Nazwa użytkownika</dt>
    <dd><?php echo($user->username); ?></dd>
    <dt>Imie</dt>
    <dd><?php echo($user->first_name); ?></dd>
    <dt>Nazwisko</dt>
    <dd><?php echo($user->last_name); ?></dd>
    <dt>Administrator</dt>
    <dd><?php echo ($user->is_admin) ? 'tak' : 'nie'; ?></dd>
    <dt>Data rejestracji</dt>
    <dd><?php echo($user->created); ?></dd>
</dl>
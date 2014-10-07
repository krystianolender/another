<?php
    $this->form->setForm($editForm);
    $this->form->setSeparatorBetweenElements("\n".'<br/>');

    $this->form->addLabel('username', 'Nazwa uzytkownika:');
    $this->form->addLabel('password', 'Haslo:');
    $this->form->addLabel('password_confirmation', 'Potwierdzenie hasla:');
    $this->form->addLabel('first_name', 'Imie:');
    $this->form->addLabel('last_name', 'Nazwisko:');
    $this->form->addLabel('wyslij', '');

    echo ($this->form->getHtml());
?>
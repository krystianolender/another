<?php
    $this->form->setForm($loginForm);
    $this->form->setSeparatorBetweenElements("\n" . '<br/>');

    $this->form->addLabel('username', 'Nazwa uzytkownika:');
    $this->form->addLabel('password', 'Haslo:');

    echo ($this->form->getHtml());
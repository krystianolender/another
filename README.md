Another to framework PHP.
Jest to framework PHP. Tworzyłem go jakiś czas temu jako forma sprawdzenia swojej umiejętności w programowaniu obiektowym i chęci sprawdzenia różnych rozwiązań programistycznych jakie mogą mieć zastosowanie w tego typu aplikacjach. By nie przedłużać, bo kod w takich sytuacja mówi sam za siebie wymienię tylko najważniejsze cechy prezentowanego projektu:
- framework jest wyłącznie mojego autorstwa
- tworzyłem go z wykorzystaniem zasady "czarnej skrzynki" wzorując się jedynie na innych frameworkach w kwestiach interfejsu i nazewnictwa niektórych klas (trudno by było inaczej, jeśli programowałem już w CakePHP i Zend Framework - niektóre rozwiązania wtedy wydają się oczywiste i przejrzyste)
- framework w swoim założeniu miał być znacznie bardziej obiektowy dla klienta końcowego niż CakePHP (co jest moim głównym zarzutem do tego frameworka)
- starałem się trzymać zasady pojedynczej odpowiedzialności klas (dlatego np. napisałem osobne walidatory formularzy zamiast obarczać tym samych formularzy) i separacji działania od widoku (przykładem może być osobny helper widoków dla formularzy z komunikatami, generowaniem htmla itp i osobna klasa samego formularza)
- przejąłem z CakePHP nawyk tworzenia notacji dla najpopularniejszych czynności programistycznych klienta (np. dodawanie domyślnego modelu do kontrolera, automatyczne "odgadywanie" widoku dla akcji, dodawanie przycisku wysyłania formularza, jeżeli został pominięty itp.)
- framework nie jest obecnie w fazie gotowej do użytku brakuje mu takich elementów jak zapewnienie bezpieczeństwa (zostawiłem to sobie na koniec, mam kilka publikacji w domu na ten temat, które czekają na swoją kolej), nie ma klasy konfiguracji, cachowania. Skupiłem się w pierwszej kolejności na realizacji MVC, zarządzaniu użytkownikami (autentykacja, autoryzacja), formularzami (walidacja, dekorowanie)
- model jest realizowany na klasie PDO co daje sporą elastyczność w doborze bazy danych, ale obecnie pracuje na PostgreSQL

Konfiguracja dla bazy danej jest następująca: użytkownik - admin, hasło - admin, baza danych - another
Konfigurację można zmienić w pliku Model.php (rozwiązanie robocze).

Skrypt bazy danych znajduje się w pliku skrypt_do_bazy_danych.sql

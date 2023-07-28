<?php
                          include_once('functions.php');
                          print("che ne sooo");
                          print($f_test);

                          //$db = open_pg_connection();
                          echo 'qui';
                            if ($db) {
                            print('connessione al database effettuata correttamente\n');
                            } else {
	                        print('errore accesso db');
                            //exit;
                            }
                            print("qui1");

                            $sql = "SELECT * FROM pigeu.utente";
                            $result = pg_query($db,$sql);

                            while ($row = pg_fetch_assoc($result)){
                                print_r($row);
                            }

                            print("qui");

                        ?>
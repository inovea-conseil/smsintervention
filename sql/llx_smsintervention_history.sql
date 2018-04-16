create table llx_smsintervention_history (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_fichinter integer NOT NULL,
  fk_user integer NOT NULL,
  status_fichinter varchar(45) NOT NULL,
  num_envoi varchar(45) NOT NULL,
  content varchar(350) NOT NULL,
  date timestamp NOT NULL
)ENGINE=innodb;
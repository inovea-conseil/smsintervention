DELETE FROM llx_const WHERE name='SMSINTERVENTION_APPLICATION_KEY';
DELETE FROM llx_const WHERE name='SMSINTERVENTION_APPLICATION_SECRET';
DELETE FROM llx_const WHERE name='SMSINTERVENTION_CONSUMER_KEY';
DELETE FROM llx_const WHERE name='SMSINTERVENTION_SMS_CONTENT';
INSERT INTO llx_const (name, value, type, note, visible, entity) VALUES ('SMSINTERVENTION_APPLICATION_KEY','','chaine','SmsIntervention Application Key',0,1);
INSERT INTO llx_const (name, value, type, note, visible, entity) VALUES ('SMSINTERVENTION_APPLICATION_SECRET','','chaine','SmsIntervention Application Secret',0,1);
INSERT INTO llx_const (name, value, type, note, visible, entity) VALUES ('SMSINTERVENTION_CONSUMER_KEY','','chaine','SmsIntervention Consumer Key',0,1);
INSERT INTO llx_const (name, value, type, note, visible, entity) VALUES ('SMSINTERVENTION_SMS_CONTENT','','chaine','SmsIntervention SMS content',0,1);
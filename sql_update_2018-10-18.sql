ALTER TABLE `wp_bts_genres` ADD `short_name` VARCHAR(10) NULL AFTER `name`;

UPDATE `wp_bts_genres` SET `short_name` = 'VtM:C' WHERE `wp_bts_genres`.`id` = 6;
UPDATE `wp_bts_genres` SET `short_name` = 'VtM:I' WHERE `wp_bts_genres`.`id` = 7;
UPDATE `wp_bts_genres` SET `short_name` = 'VtM:S' WHERE `wp_bts_genres`.`id` = 8;
UPDATE `wp_bts_genres` SET `short_name` = 'VtR CofD' WHERE `wp_bts_genres`.`id` = 12;
UPDATE `wp_bts_genres` SET `short_name` = 'MtA CofD' WHERE `wp_bts_genres`.`id` = 13;
UPDATE `wp_bts_genres` SET `short_name` = 'WtF CofD' WHERE `wp_bts_genres`.`id` = 14;
UPDATE `wp_bts_genres` SET `short_name` = 'X CofD' WHERE `wp_bts_genres`.`id` = 16;

CREATE TABLE `wp_bts_offices` ( `id` BIGINT(20) NOT NULL AUTO_INCREMENT , `title` VARCHAR(255) NOT NULL , `short_form` VARCHAR(10) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

INSERT INTO wp_bts_offices
        (title, short_form)
VALUES
        ("Assistant Domain Coordinator", "ADC"),
        ("Assistant Domain Storyteller", "ADST"),
        ("Assistant Deputy National Coordinator", "ADNC"),
        ("Assistant Deputy National Storyteller", "ADNST"),
        ("Assitant National Communications Coordinator", "ANCC"),
        ("Assistant National Coordinator", "ANC"),
        ("Assistant National Membership Coordinator", "ANMC"),
        ("Assistant National Promotions Coordinator", "ANPC"),
        ("Assitant National Rewards Coordinator", "ANRC"),
        ("Assistant National Storyteller", "ANST"),
        ("Assistant Venue  Storyteller", "AVST"),
        ("Assistant Venue Coordinator", "AVC"),
        ("Deputy National Coordinator", "DNC"),
        ("Deputy National Storyteller", "DNST"),
        ("Domain Coordinator", "DC"),
        ("Domain Storyteller", "DST"),
        ("Event Communications Coordinator", "ECC"),
        ("Event Financial Coordinator", "EFC"),
        ("Event Promotions Coordinator", "EPC"),
        ("Event Venue Coordinator", "EVC"),
        ("Genre Storyteller", "GST"),
        ("Lead Event Coordinator", "LEC"),
        ("Legacy NRC Officer", "LNRC"),
        ("Magazine Editor", "ME"),
        ("Narrator", "N"),
        ("National Communications Coordinator", "NCC"),
        ("National Finance Coordinator", "NFC"),
        ("National Legal Officer", "NLO"),
        ("National Membership Coordinator", "NMC"),
        ("National Promotions Coordinator", "NPC"),
        ("National Rewards Coordinator", "NRC"),
        ("National Storyteller", "NST"),
        ("Venue Coordinator", "VC"),
        ("Venue Storyteller", "VST");

ALTER TABLE wp_bts_officers
	ADD COLUMN id_offices bigint(20) NULL AFTER id_domains,
	ADD FOREIGN KEY fk_id_offices (id_offices) REFERENCES wp_bts_offices(id);

UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ADC") WHERE title = "﻿Acting Domain Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ADST") WHERE title = "ADST";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ADNC") WHERE title = "Assistant Deputy National Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ADNST") WHERE title = "Assistant Deputy National Storyteller";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ADC") WHERE title = "Assistant Domain Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ADC") WHERE title = "Assistant Domain Coordinator - Assets & Inventory";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ADC") WHERE title = "Assistant Domain Coordinator - Canteen";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ADC") WHERE title = "Assistant Domain Coordinator - Venues";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ADST") WHERE title = "Assistant Domain Storyteller";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ADST") WHERE title = "Assistant Domain Storyteller Awakening";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ANCC") WHERE title = "assistant National Communications Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ANCC") WHERE title = "assistant National Communications Coordinator (Forums)";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ANCC") WHERE title = "assistant National Communications Coordinator (Lists)";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ANCC") WHERE title = "assistant National Communications Coordinator (Web Admin)";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ANCC") WHERE title = "assistant National Communications Coordinator (Wiki Admin)";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ANC") WHERE title = "assistant National Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ANC") WHERE title = "assistant National Coordinator (Associations)";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ANMC") WHERE title = "assistant National Membership Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ANPC") WHERE title = "assistant National Promotions Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ANRC") WHERE title = "assistant National Rewards Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ANST") WHERE title = "assistant National Storyteller";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "AVST") WHERE title = "Assistant Venue  Storyteller";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "AVC") WHERE title = "Assistant Venue Co-ordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "AVC") WHERE title = "Assistant Venue Co-ordinator Venue Liason";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "AVC") WHERE title = "Assistant Venue Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "AVC") WHERE title = "Assistant Venue Coordinator - City of Lights";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "AVST") WHERE title = "Assistant Venue Storyteller";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "AVST") WHERE title = "Assistant Venue Storyteller - Brisbane Camarilla";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "AVST") WHERE title = "Assistant Venue Storyteller - Canberra, Werewolf: Provocation";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "AVST") WHERE title = "Assistant Venue Storyteller - Dates and Times";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "AVST") WHERE title = "Assistant Venue Storyteller - Rules";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "DNC") WHERE title = "Deputy National Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "DNST") WHERE title = "Deputy National Storyteller";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "DC") WHERE title = "Domain Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "DST") WHERE title = "Domain Storyteller";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ECC") WHERE title = "Event Communications Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "EFC") WHERE title = "Event Financial Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "EPC") WHERE title = "Event Promotions Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "EVC") WHERE title = "Event Venue Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "GST") WHERE title = "Genre Storyteller:";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "GST") WHERE title = "Genre Storyteller: Changeling";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "GST") WHERE title = "Genre Storyteller: Classic World of Darkness";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "GST") WHERE title = "Genre Storyteller: Forsaken CofD";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "GST") WHERE title = "Genre Storyteller: Mage the Awakening CofD";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "GST") WHERE title = "Genre Storyteller: Requiem";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "GST") WHERE title = "Genre Storyteller: Requiem CofD";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "LEC") WHERE title = "Lead Event Coordinator - Conclave";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "LEC") WHERE title = "Lead Event Coordinator - Wintercon";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "LEC") WHERE title = "Lead Event Storyteller - Conclave";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "LEC") WHERE title = "Lead Event Storyteller - Wintercon";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "LNRC") WHERE title = "Legacy NRC officer";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ME") WHERE title = "Magazine Editor 1";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ME") WHERE title = "Magazine Editor 2";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ME") WHERE title = "Magazine Editor 3";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ME") WHERE title = "Magazine Editor 4";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ME") WHERE title = "Magazine Editor 5";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "N") WHERE title = "Narrator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NCC") WHERE title = "National Communications Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NCC") WHERE title = "National Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NFC") WHERE title = "National Finance Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NLO") WHERE title = "National Legal Officer";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NMC") WHERE title = "National Membership Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NPC") WHERE title = "National Promotions Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NRC") WHERE title = "National Rewards Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NST") WHERE title = "National Storyteller";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "ANMC") WHERE title = "NMC Access Assistant";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "DC") WHERE title = "Outgoing DC";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "DNC") WHERE title = "Outgoing Deputy National Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "DNST") WHERE title = "Outgoing Deputy National Storyteller";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NCC") WHERE title = "Outgoing National Communications Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NCC") WHERE title = "Outgoing National Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NMC") WHERE title = "Outgoing National Membership Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NPC") WHERE title = "Outgoing National Promotions Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NRC") WHERE title = "Outgoing National Rewards Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "NST") WHERE title = "Outgoing National Storyteller";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "VC") WHERE title = "Venue  Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "VC") WHERE title = "Venue Co-ordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "VC") WHERE title = "Venue Coordinator";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "VC") WHERE title = "Venue Coordinator - City of Lights";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "VST") WHERE title = "Venue Storyteller";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "VST") WHERE title = "Venue Storyteller - City of Lights";
UPDATE wp_bts_officers SET id_offices = (SELECT id FROM wp_bts_offices WHERE short_form = "VST") WHERE title = "Venue Storyteller: HellCastle";

ALTER TABLE `wp_bts_offices` ADD `chain` ENUM('Coordinator','Storyteller') NOT NULL DEFAULT 'Coordinator' AFTER `short_form`;
UPDATE wp_bts_offices offices INNER JOIN wp_bts_officers officers ON (officers.id_offices = offices.id) SET offices.chain = officers.chain;
ALTER TABLE `wp_bts_officers` DROP `chain`;
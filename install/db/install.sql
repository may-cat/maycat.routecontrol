CREATE TABLE IF NOT EXISTS `n_routecontrol_menulock` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PATH` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `SITE_ID` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `TIMESTAMP_X` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='' AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `n_routecontrol_accesslock` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PATH` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `SITE_ID` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `TIMESTAMP_X` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='' AUTO_INCREMENT=1 ;


--
-- Structure for view `car_makes_v`
--
DROP VIEW IF EXISTS `car_makes_v`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `car_makes_v` AS select `car_makes`.`car_make_id` AS `car_make_id`,`car_makes`.`car_make_name` AS `car_make_name` from `car_makes` where (`car_makes`.`state_enum` not in (_utf8'Alternativ',_utf8'Slettet')) order by `car_makes`.`car_make_name`;

-- --------------------------------------------------------

-- Structure for view `car_models_v`
--
DROP VIEW IF EXISTS `car_models_v`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `car_models_v` AS select `cmo`.`car_make_id` AS `car_make_id`,`cma`.`car_make_name` AS `car_make_name`,`cmo`.`car_model_id` AS `car_model_id`,`cmo`.`car_model_name` AS `car_model_name`,trim(ifnull(`cmo`.`model_cleansed_name`,`cmo`.`car_model_name`)) AS `model_cleansed_name`,`cmo`.`state_enum` AS `state_enum`,`cmo`.`car_model_main_id` AS `car_model_main_id` from (`car_makes` `cma` join `car_models` `cmo`) where ((`cma`.`car_make_id` = `cmo`.`car_make_id`) and isnull(`cma`.`car_make_main_id`) and (`cma`.`state_enum` <> _utf8'Slettet')) union select `cma_alt`.`car_make_main_id` AS `car_make_id`,_utf8'CMA' AS `car_make_name`,`cmo_alt`.`car_model_id` AS `car_model_id`,`cmo_alt`.`car_model_name` AS `car_model_name`,trim(ifnull(`cmo_alt`.`model_cleansed_name`,`cmo_alt`.`car_model_name`)) AS `model_cleansed_name`,`cmo_alt`.`state_enum` AS `state_enum`,`cmo_alt`.`car_model_main_id` AS `car_model_main_id` from (`car_makes` `cma_alt` join `car_models` `cmo_alt`) where ((`cma_alt`.`car_make_id` = `cmo_alt`.`car_make_id`) and (`cma_alt`.`car_make_main_id` is not null) and (`cma_alt`.`state_enum` <> _utf8'Slettet'));


--
-- Structure for view `car_models_sorted_v`
--
DROP VIEW IF EXISTS `car_models_sorted_v`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `car_models_sorted_v` AS select `car_models_v`.`car_make_id` AS `car_make_id`,`car_models_v`.`car_make_name` AS `car_make_name`,`car_models_v`.`car_model_id` AS `car_model_id`,`car_models_v`.`car_model_name` AS `car_model_name`,`car_models_v`.`model_cleansed_name` AS `model_cleansed_name`,`car_models_v`.`state_enum` AS `state_enum`,`car_models_v`.`car_model_main_id` AS `car_model_main_id` from `car_models_v` order by 2,5;

-- --------------------------------------------------------

--

-- --------------------------------------------------------

--
-- Structure for view `crm_supplier_month_stats`
--
DROP VIEW IF EXISTS `crm_supplier_month_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `crm_supplier_month_stats` AS select month(`jts`.`created`) AS `month`,`sps`.`supplier_name` AS `supplier_name`,count(`xr`.`user_agent_id`) AS `unique_users`,count(1) AS `visits`,sum(`jts`.`price_inc_vat`) AS `sum(jts.price_inc_vat)`,`sps`.`spare_part_supplier_id` AS `spare_part_supplier_id` from ((`jump_to_shop` `jts` join `xml_http_request` `xr`) join `spare_part_suppliers` `sps`) where ((`sps`.`spare_part_supplier_id` = `jts`.`spare_part_supplier_id`) and (`jts`.`xml_http_request_id` = `xr`.`xml_http_request_id`)) group by month(`jts`.`created`),`sps`.`supplier_name` with rollup;

-- --------------------------------------------------------

--
-- Structure for view `crm_supplier_total_stats`
--
DROP VIEW IF EXISTS `crm_supplier_total_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `crm_supplier_total_stats` AS select `jts`.`spare_part_supplier_id` AS `spare_part_supplier_id`,`sps`.`supplier_name` AS `supplier_name`,sum(`jts`.`price_inc_vat`) AS `sum(jts.price_inc_vat)`,count(1) AS `count(1)` from (`jump_to_shop` `jts` join `spare_part_suppliers` `sps`) where (`sps`.`spare_part_supplier_id` = `jts`.`spare_part_supplier_id`) group by `jts`.`spare_part_supplier_id`,`sps`.`supplier_name` with rollup;

-- --------------------------------------------------------

--
-- Structure for view `info_spp_price_stat_v`
--
DROP VIEW IF EXISTS `info_spp_price_stat_v`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `info_spp_price_stat_v` AS select `sps`.`supplier_name` AS `supplier_name`,count(`spp`.`spare_part_price_id`) AS `price_count`,`spp`.`spare_part_supplier_id` AS `spare_part_supplier_id`,min(`spp`.`updated`) AS `oldest`,max(`spp`.`updated`) AS `newest` from (`spare_part_prices` `spp` join `spare_part_suppliers` `sps` on((`spp`.`spare_part_supplier_id` = `sps`.`spare_part_supplier_id`))) group by `spp`.`spare_part_supplier_id`;

-- --------------------------------------------------------

--
-- Structure for view `seo_google_revisits`
--
DROP VIEW IF EXISTS `seo_google_revisits`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `seo_google_revisits` AS select `seo_google_visits`.`server_request_uri` AS `server_request_uri`,count(`seo_google_visits`.`xml_http_request_id`) AS `revisits`,min(`seo_google_visits`.`created`) AS `FIRST`,max(`seo_google_visits`.`created`) AS `last` from `seo_google_visits` where 1 group by 1 order by 2 desc;

-- --------------------------------------------------------

--
-- Structure for view `seo_google_visits`
--
DROP VIEW IF EXISTS `seo_google_visits`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `seo_google_visits` AS select `xml_http_request`.`xml_http_request_id` AS `xml_http_request_id`,`xml_http_request`.`created_by` AS `created_by`,`xml_http_request`.`request_payload` AS `request_payload`,`xml_http_request`.`updated` AS `updated`,`xml_http_request`.`created` AS `created`,`xml_http_request`.`first_response` AS `first_response`,`xml_http_request`.`latest_response` AS `latest_response`,`xml_http_request`.`server_request_uri` AS `server_request_uri`,`xml_http_request`.`unit_test_name` AS `unit_test_name`,`xml_http_request`.`unit_test_description` AS `unit_test_description`,`xml_http_request`.`user_info` AS `user_info`,`xml_http_request`.`client_ip` AS `client_ip`,`xml_http_request`.`user_agent` AS `user_agent`,`xml_http_request`.`http_cookie` AS `http_cookie`,`xml_http_request`.`request_time_taken_ms` AS `request_time_taken_ms`,`xml_http_request`.`data_rows_returned` AS `data_rows_returned`,`xml_http_request`.`sql_used` AS `sql_used`,`xml_http_request`.`q` AS `q`,`xml_http_request`.`car_model_id` AS `car_model_id`,`xml_http_request`.`car_make_id` AS `car_make_id`,`xml_http_request`.`trace` AS `trace`,`xml_http_request`.`http_referer` AS `http_referer`,`xml_http_request`.`user_agent_id` AS `user_agent_id` from `xml_http_request` where (`xml_http_request`.`user_agent_id` = 2);

-- --------------------------------------------------------

--
-- Structure for view `seo_google_visits_pr_day`
--
DROP VIEW IF EXISTS `seo_google_visits_pr_day`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `seo_google_visits_pr_day` AS select cast(`seo_google_visits`.`created` as date) AS `date`,count(`seo_google_visits`.`xml_http_request_id`) AS `visits` from `seo_google_visits` group by 1;

-- --------------------------------------------------------

--
-- Structure for view `seo_google_visits_pr_hour`
--
DROP VIEW IF EXISTS `seo_google_visits_pr_hour`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `seo_google_visits_pr_hour` AS select cast(`seo_google_visits`.`updated` as date) AS `date`,hour(`seo_google_visits`.`updated`) AS `time`,count(`seo_google_visits`.`xml_http_request_id`) AS `count` from `seo_google_visits` where 1 group by 1,2 order by 1 desc,2 desc;

-- --------------------------------------------------------

--
-- Structure for view `spare_part_prices_doubles`
--
DROP VIEW IF EXISTS `spare_part_prices_doubles`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spare_part_prices_doubles` AS select `spare_part_prices`.`name` AS `name`,`spare_part_prices`.`supplier_part_number` AS `supplier_part_number`,`spare_part_prices`.`price_inc_vat` AS `price_inc_vat`,min(`spare_part_prices`.`created`) AS `first`,max(`spare_part_prices`.`created`) AS `last`,min(`spare_part_prices`.`spare_part_price_id`) AS `first_id`,max(`spare_part_prices`.`spare_part_price_id`) AS `last_id`,count(`spare_part_prices`.`spare_part_price_id`) AS `count` from `spare_part_prices` where 1 group by 1,2 order by count(`spare_part_prices`.`spare_part_price_id`) desc;

-- --------------------------------------------------------

--
-- Structure for view `spare_part_prices_redundant`
--
DROP VIEW IF EXISTS `spare_part_prices_redundant`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spare_part_prices_redundant` AS select `s`.`spare_part_price_id` AS `spare_part_price_id`,`d`.`name` AS `name`,`d`.`supplier_part_number` AS `supplier_part_number`,`d`.`price_inc_vat` AS `price_inc_vat`,`d`.`first` AS `first`,`d`.`last` AS `last`,`d`.`first_id` AS `first_id`,`d`.`last_id` AS `last_id`,`d`.`count` AS `count` from (`spare_part_prices_doubles` `d` join `spare_part_prices` `s`) where (1 and (`d`.`supplier_part_number` = `s`.`supplier_part_number`) and (`d`.`first_id` <> `s`.`spare_part_price_id`));

-- --------------------------------------------------------

--
-- Structure for view `spare_part_prices_sup`
--
DROP VIEW IF EXISTS `spare_part_prices_sup`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spare_part_prices_sup` AS select `sps`.`supplier_name` AS `supplier_name`,`spp`.`spare_part_price_id` AS `spare_part_price_id`,`spp`.`name` AS `name`,`spp`.`description` AS `description`,`spp`.`spare_part_url` AS `spare_part_url`,`spp`.`spare_part_image_url` AS `spare_part_image_url`,`spp`.`spare_part_category_id` AS `spare_part_category_id`,`spp`.`spare_part_category_free_text` AS `spare_part_category_free_text`,`spp`.`part_placement` AS `part_placement`,`spp`.`part_placement_left_right` AS `part_placement_left_right`,`spp`.`part_placement_front_back` AS `part_placement_front_back`,`spp`.`suplier_part_number` AS `suplier_part_number`,`spp`.`original_part_number` AS `original_part_number`,`spp`.`price_inc_vat` AS `price_inc_vat`,`spp`.`producer_make_name` AS `producer_make_name`,`spp`.`producer_part_number` AS `producer_part_number`,`spp`.`created` AS `created`,`spp`.`created_by` AS `created_by`,`spp`.`spare_part_supplier_id` AS `spare_part_supplier_id`,`sps`.`state` AS `state` from (`spare_part_prices` `spp` left join `spare_part_suppliers` `sps` on((`sps`.`spare_part_supplier_id` = `spp`.`spare_part_supplier_id`)));

-- --------------------------------------------------------

--
-- Structure for view `spp_longest_names`
--
DROP VIEW IF EXISTS `spp_longest_names`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spp_longest_names` AS select `spp`.`spare_part_price_id` AS `spare_part_price_id`,`spp`.`name` AS `name`,`spp`.`spare_part_supplier_id` AS `sps_id`,`spp`.`spare_part_url` AS `spare_part_url`,`spp`.`price_parser_run_id` AS `price_parser_run_id`,`ppr`.`file_base_name` AS `file_base_name` from (`spare_part_prices` `spp` join `price_parser_runs` `ppr` on((`ppr`.`price_parser_run_id` = `spp`.`price_parser_run_id`))) where (length(`spp`.`name`) > 25) order by length(`spp`.`name`) desc;

-- --------------------------------------------------------

--
-- Structure for view `spp_prices`
--
DROP VIEW IF EXISTS `spp_prices`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spp_prices` AS select count(0) AS `priser`,`sps_spp_cm2spp_cmo_v`.`supplier_name` AS `butik`,`sps_spp_cm2spp_cmo_v`.`spare_part_supplier_id` AS `butik_id` from `sps_spp_cm2spp_cmo_v` group by `sps_spp_cm2spp_cmo_v`.`spare_part_supplier_id`;

-- --------------------------------------------------------

--
-- Structure for view `spp_pr_models_v`
--
DROP VIEW IF EXISTS `spp_pr_models_v`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spp_pr_models_v` AS select `p`.`car_make_name` AS `car_make_name`,`p`.`car_model_id` AS `car_model_id`,`p`.`car_model_name` AS `car_model_name`,count(`p`.`spare_part_price_id`) AS `prices` from `sps_spp_cm2spp_cmo_v` `p` group by 1,2,3 order by 1,2;

-- --------------------------------------------------------

--
-- Structure for view `spp_simple_runs`
--
DROP VIEW IF EXISTS `spp_simple_runs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spp_simple_runs` AS select count(0) AS `Rækker`,`spare_part_prices`.`spare_part_supplier_id` AS `spare_part_supplier_id`,`spare_part_prices`.`price_parser_run_id` AS `price_parser_run_id`,min(`spare_part_prices`.`created`) AS `started`,max(`spare_part_prices`.`created`) AS `latest` from `spare_part_prices` group by 2,3 with rollup;

-- --------------------------------------------------------

--
-- Structure for view `sps_spp_cm2spp_cmo_v`
--
DROP VIEW IF EXISTS `sps_spp_cm2spp_cmo_v`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `sps_spp_cm2spp_cmo_v` AS select `sps`.`supplier_name` AS `supplier_name`,`cmo`.`car_make_name` AS `car_make_name`,`cmo`.`car_model_name` AS `car_model_name`,`cmo`.`car_model_id` AS `car_model_id`,ifnull(`cmo`.`car_model_main_id`,`cmo`.`car_model_id`) AS `car_model_clean_id`,`spp`.`spare_part_price_id` AS `spare_part_price_id`,`spp`.`name` AS `name`,`spp`.`description` AS `description`,`spp`.`spare_part_url` AS `spare_part_url`,`spp`.`spare_part_image_url` AS `spare_part_image_url`,`spp`.`spare_part_category_id` AS `spare_part_category_id`,`spp`.`spare_part_category_free_text` AS `spare_part_category_free_text`,`spp`.`part_placement` AS `part_placement`,`spp`.`part_placement_left_right` AS `part_placement_left_right`,`spp`.`part_placement_front_back` AS `part_placement_front_back`,`spp`.`supplier_part_number` AS `supplier_part_number`,`spp`.`original_part_number` AS `original_part_number`,`spp`.`price_inc_vat` AS `price_inc_vat`,`spp`.`producer_make_name` AS `producer_make_name`,`spp`.`producer_part_number` AS `producer_part_number`,`spp`.`spare_part_supplier_id` AS `spare_part_supplier_id`,`cm2spp`.`year_from` AS `year_from`,`cm2spp`.`month_from` AS `month_from`,`cm2spp`.`year_to` AS `year_to`,`cm2spp`.`month_to` AS `month_to`,`cm2spp`.`chassis_no_from` AS `chassis_no_from`,`cm2spp`.`chassis_no_to` AS `chassis_no_to` from (((`spare_part_prices` `spp` join `spare_part_suppliers` `sps` on((`sps`.`spare_part_supplier_id` = `spp`.`spare_part_supplier_id`))) left join `car_models_to_spare_part_prices` `cm2spp` on((`spp`.`spare_part_price_id` = `cm2spp`.`spare_part_price_id`))) join `car_models_v` `cmo` on((`cm2spp`.`car_model_id` = `cmo`.`car_model_id`)));

-- --------------------------------------------------------

--
-- Structure for view `user_agent_grouped_by_agent_info`
--
DROP VIEW IF EXISTS `user_agent_grouped_by_agent_info`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `user_agent_grouped_by_agent_info` AS select `user_agents`.`user_agent_info` AS `user_agent_info`,count(1) AS `count`,min(`user_agents`.`created`) AS `FIRST`,max(`user_agents`.`created`) AS `last` from `user_agents` group by `user_agents`.`user_agent_info` having (count(1) > 1) order by 2 desc;

-- --------------------------------------------------------

--
-- Structure for view `user_agent_newest_with_age`
--
DROP VIEW IF EXISTS `user_agent_newest_with_age`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `user_agent_newest_with_age` AS select timestampdiff(MINUTE,`ua`.`created`,sysdate()) AS `age_mins`,timestampdiff(HOUR,`ua`.`created`,sysdate()) AS `age_hours`,timestampdiff(DAY,`ua`.`created`,sysdate()) AS `age_Days`,`ua`.`user_agent_id` AS `user_agent_id`,`ua`.`user_agent_info` AS `user_agent_info`,`ua`.`cookie_guid` AS `cookie_guid`,`ua`.`created` AS `created`,`ua`.`ip_addr_first_visit` AS `ip_addr_first_visit`,`ua`.`is_bot` AS `is_bot`,`ua`.`end_user_id` AS `end_user_id`,`ua`.`internal_note` AS `internal_note`,`ua`.`next_user_greting` AS `next_user_greting`,`ua`.`allow_remember_user_name` AS `allow_remember_user_name`,`ua`.`allow_automatic_login` AS `allow_automatic_login` from `user_agents` `ua` where ((not((`ua`.`user_agent_info` like _utf8'%bot%'))) and (not((`ua`.`user_agent_info` like _utf8'%spider%')))) order by `ua`.`created` desc;

-- --------------------------------------------------------

--
-- Structure for view `xml_http_request_jump_to`
--
DROP 
--
-- Structure for view `car_makes_v`
--
DROP VIEW IF EXISTS `car_makes_v`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `car_makes_v` AS select `car_makes`.`car_make_id` AS `car_make_id`,`car_makes`.`car_make_name` AS `car_make_name` from `car_makes` where (`car_makes`.`state_enum` not in (_utf8'Alternativ',_utf8'Slettet')) order by `car_makes`.`car_make_name`;

-- --------------------------------------------------------

--
-- Structure for view `car_models_sorted_v`
--
DROP VIEW IF EXISTS `car_models_sorted_v`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `car_models_sorted_v` AS select `car_models_v`.`car_make_id` AS `car_make_id`,`car_models_v`.`car_make_name` AS `car_make_name`,`car_models_v`.`car_model_id` AS `car_model_id`,`car_models_v`.`car_model_name` AS `car_model_name`,`car_models_v`.`model_cleansed_name` AS `model_cleansed_name`,`car_models_v`.`state_enum` AS `state_enum`,`car_models_v`.`car_model_main_id` AS `car_model_main_id` from `car_models_v` order by 2,5;

-- --------------------------------------------------------

--
-- Structure for view `car_models_v`
--
DROP VIEW IF EXISTS `car_models_v`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `car_models_v` AS select `cmo`.`car_make_id` AS `car_make_id`,`cma`.`car_make_name` AS `car_make_name`,`cmo`.`car_model_id` AS `car_model_id`,`cmo`.`car_model_name` AS `car_model_name`,trim(ifnull(`cmo`.`model_cleansed_name`,`cmo`.`car_model_name`)) AS `model_cleansed_name`,`cmo`.`state_enum` AS `state_enum`,`cmo`.`car_model_main_id` AS `car_model_main_id` from (`car_makes` `cma` join `car_models` `cmo`) where ((`cma`.`car_make_id` = `cmo`.`car_make_id`) and isnull(`cma`.`car_make_main_id`) and (`cma`.`state_enum` <> _utf8'Slettet')) union select `cma_alt`.`car_make_main_id` AS `car_make_id`,_utf8'CMA' AS `car_make_name`,`cmo_alt`.`car_model_id` AS `car_model_id`,`cmo_alt`.`car_model_name` AS `car_model_name`,trim(ifnull(`cmo_alt`.`model_cleansed_name`,`cmo_alt`.`car_model_name`)) AS `model_cleansed_name`,`cmo_alt`.`state_enum` AS `state_enum`,`cmo_alt`.`car_model_main_id` AS `car_model_main_id` from (`car_makes` `cma_alt` join `car_models` `cmo_alt`) where ((`cma_alt`.`car_make_id` = `cmo_alt`.`car_make_id`) and (`cma_alt`.`car_make_main_id` is not null) and (`cma_alt`.`state_enum` <> _utf8'Slettet'));

-- --------------------------------------------------------

--
-- Structure for view `crm_supplier_month_stats`
--
DROP VIEW IF EXISTS `crm_supplier_month_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `crm_supplier_month_stats` AS select month(`jts`.`created`) AS `month`,`sps`.`supplier_name` AS `supplier_name`,count(`xr`.`user_agent_id`) AS `unique_users`,count(1) AS `visits`,sum(`jts`.`price_inc_vat`) AS `sum(jts.price_inc_vat)`,`sps`.`spare_part_supplier_id` AS `spare_part_supplier_id` from ((`jump_to_shop` `jts` join `xml_http_request` `xr`) join `spare_part_suppliers` `sps`) where ((`sps`.`spare_part_supplier_id` = `jts`.`spare_part_supplier_id`) and (`jts`.`xml_http_request_id` = `xr`.`xml_http_request_id`)) group by month(`jts`.`created`),`sps`.`supplier_name` with rollup;

-- --------------------------------------------------------

--
-- Structure for view `crm_supplier_total_stats`
--
DROP VIEW IF EXISTS `crm_supplier_total_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `crm_supplier_total_stats` AS select `jts`.`spare_part_supplier_id` AS `spare_part_supplier_id`,`sps`.`supplier_name` AS `supplier_name`,sum(`jts`.`price_inc_vat`) AS `sum(jts.price_inc_vat)`,count(1) AS `count(1)` from (`jump_to_shop` `jts` join `spare_part_suppliers` `sps`) where (`sps`.`spare_part_supplier_id` = `jts`.`spare_part_supplier_id`) group by `jts`.`spare_part_supplier_id`,`sps`.`supplier_name` with rollup;

-- --------------------------------------------------------

--
-- Structure for view `info_spp_price_stat_v`
--
DROP VIEW IF EXISTS `info_spp_price_stat_v`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `info_spp_price_stat_v` AS select `sps`.`supplier_name` AS `supplier_name`,count(`spp`.`spare_part_price_id`) AS `price_count`,`spp`.`spare_part_supplier_id` AS `spare_part_supplier_id`,min(`spp`.`updated`) AS `oldest`,max(`spp`.`updated`) AS `newest` from (`spare_part_prices` `spp` join `spare_part_suppliers` `sps` on((`spp`.`spare_part_supplier_id` = `sps`.`spare_part_supplier_id`))) group by `spp`.`spare_part_supplier_id`;

-- --------------------------------------------------------

--
-- Structure for view `seo_google_revisits`
--
DROP VIEW IF EXISTS `seo_google_revisits`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `seo_google_revisits` AS select `seo_google_visits`.`server_request_uri` AS `server_request_uri`,count(`seo_google_visits`.`xml_http_request_id`) AS `revisits`,min(`seo_google_visits`.`created`) AS `FIRST`,max(`seo_google_visits`.`created`) AS `last` from `seo_google_visits` where 1 group by 1 order by 2 desc;

-- --------------------------------------------------------

--
-- Structure for view `seo_google_visits`
--
DROP VIEW IF EXISTS `seo_google_visits`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `seo_google_visits` AS select `xml_http_request`.`xml_http_request_id` AS `xml_http_request_id`,`xml_http_request`.`created_by` AS `created_by`,`xml_http_request`.`request_payload` AS `request_payload`,`xml_http_request`.`updated` AS `updated`,`xml_http_request`.`created` AS `created`,`xml_http_request`.`first_response` AS `first_response`,`xml_http_request`.`latest_response` AS `latest_response`,`xml_http_request`.`server_request_uri` AS `server_request_uri`,`xml_http_request`.`unit_test_name` AS `unit_test_name`,`xml_http_request`.`unit_test_description` AS `unit_test_description`,`xml_http_request`.`user_info` AS `user_info`,`xml_http_request`.`client_ip` AS `client_ip`,`xml_http_request`.`user_agent` AS `user_agent`,`xml_http_request`.`http_cookie` AS `http_cookie`,`xml_http_request`.`request_time_taken_ms` AS `request_time_taken_ms`,`xml_http_request`.`data_rows_returned` AS `data_rows_returned`,`xml_http_request`.`sql_used` AS `sql_used`,`xml_http_request`.`q` AS `q`,`xml_http_request`.`car_model_id` AS `car_model_id`,`xml_http_request`.`car_make_id` AS `car_make_id`,`xml_http_request`.`trace` AS `trace`,`xml_http_request`.`http_referer` AS `http_referer`,`xml_http_request`.`user_agent_id` AS `user_agent_id` from `xml_http_request` where (`xml_http_request`.`user_agent_id` = 2);

-- --------------------------------------------------------

--
-- Structure for view `seo_google_visits_pr_day`
--
DROP VIEW IF EXISTS `seo_google_visits_pr_day`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `seo_google_visits_pr_day` AS select cast(`seo_google_visits`.`created` as date) AS `date`,count(`seo_google_visits`.`xml_http_request_id`) AS `visits` from `seo_google_visits` group by 1;

-- --------------------------------------------------------

--
-- Structure for view `seo_google_visits_pr_hour`
--
DROP VIEW IF EXISTS `seo_google_visits_pr_hour`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `seo_google_visits_pr_hour` AS select cast(`seo_google_visits`.`updated` as date) AS `date`,hour(`seo_google_visits`.`updated`) AS `time`,count(`seo_google_visits`.`xml_http_request_id`) AS `count` from `seo_google_visits` where 1 group by 1,2 order by 1 desc,2 desc;

-- --------------------------------------------------------

--
-- Structure for view `spare_part_prices_doubles`
--
DROP VIEW IF EXISTS `spare_part_prices_doubles`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spare_part_prices_doubles` AS select `spare_part_prices`.`name` AS `name`,`spare_part_prices`.`supplier_part_number` AS `supplier_part_number`,`spare_part_prices`.`price_inc_vat` AS `price_inc_vat`,min(`spare_part_prices`.`created`) AS `first`,max(`spare_part_prices`.`created`) AS `last`,min(`spare_part_prices`.`spare_part_price_id`) AS `first_id`,max(`spare_part_prices`.`spare_part_price_id`) AS `last_id`,count(`spare_part_prices`.`spare_part_price_id`) AS `count` from `spare_part_prices` where 1 group by 1,2 order by count(`spare_part_prices`.`spare_part_price_id`) desc;

-- --------------------------------------------------------

--
-- Structure for view `spare_part_prices_redundant`
--
DROP VIEW IF EXISTS `spare_part_prices_redundant`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spare_part_prices_redundant` AS select `s`.`spare_part_price_id` AS `spare_part_price_id`,`d`.`name` AS `name`,`d`.`supplier_part_number` AS `supplier_part_number`,`d`.`price_inc_vat` AS `price_inc_vat`,`d`.`first` AS `first`,`d`.`last` AS `last`,`d`.`first_id` AS `first_id`,`d`.`last_id` AS `last_id`,`d`.`count` AS `count` from (`spare_part_prices_doubles` `d` join `spare_part_prices` `s`) where (1 and (`d`.`supplier_part_number` = `s`.`supplier_part_number`) and (`d`.`first_id` <> `s`.`spare_part_price_id`));

-- --------------------------------------------------------

--
-- Structure for view `spare_part_prices_sup`
--
DROP VIEW IF EXISTS `spare_part_prices_sup`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spare_part_prices_sup` AS select `sps`.`supplier_name` AS `supplier_name`,`spp`.`spare_part_price_id` AS `spare_part_price_id`,`spp`.`name` AS `name`,`spp`.`description` AS `description`,`spp`.`spare_part_url` AS `spare_part_url`,`spp`.`spare_part_image_url` AS `spare_part_image_url`,`spp`.`spare_part_category_id` AS `spare_part_category_id`,`spp`.`spare_part_category_free_text` AS `spare_part_category_free_text`,`spp`.`part_placement` AS `part_placement`,`spp`.`part_placement_left_right` AS `part_placement_left_right`,`spp`.`part_placement_front_back` AS `part_placement_front_back`,`spp`.`suplier_part_number` AS `suplier_part_number`,`spp`.`original_part_number` AS `original_part_number`,`spp`.`price_inc_vat` AS `price_inc_vat`,`spp`.`producer_make_name` AS `producer_make_name`,`spp`.`producer_part_number` AS `producer_part_number`,`spp`.`created` AS `created`,`spp`.`created_by` AS `created_by`,`spp`.`spare_part_supplier_id` AS `spare_part_supplier_id`,`sps`.`state` AS `state` from (`spare_part_prices` `spp` left join `spare_part_suppliers` `sps` on((`sps`.`spare_part_supplier_id` = `spp`.`spare_part_supplier_id`)));

-- --------------------------------------------------------

--
-- Structure for view `spp_longest_names`
--
DROP VIEW IF EXISTS `spp_longest_names`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spp_longest_names` AS select `spp`.`spare_part_price_id` AS `spare_part_price_id`,`spp`.`name` AS `name`,`spp`.`spare_part_supplier_id` AS `sps_id`,`spp`.`spare_part_url` AS `spare_part_url`,`spp`.`price_parser_run_id` AS `price_parser_run_id`,`ppr`.`file_base_name` AS `file_base_name` from (`spare_part_prices` `spp` join `price_parser_runs` `ppr` on((`ppr`.`price_parser_run_id` = `spp`.`price_parser_run_id`))) where (length(`spp`.`name`) > 25) order by length(`spp`.`name`) desc;

-- --------------------------------------------------------

--
-- Structure for view `spp_prices`
--
DROP VIEW IF EXISTS `spp_prices`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spp_prices` AS select count(0) AS `priser`,`sps_spp_cm2spp_cmo_v`.`supplier_name` AS `butik`,`sps_spp_cm2spp_cmo_v`.`spare_part_supplier_id` AS `butik_id` from `sps_spp_cm2spp_cmo_v` group by `sps_spp_cm2spp_cmo_v`.`spare_part_supplier_id`;

-- --------------------------------------------------------

--
-- Structure for view `spp_pr_models_v`
--
DROP VIEW IF EXISTS `spp_pr_models_v`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spp_pr_models_v` AS select `p`.`car_make_name` AS `car_make_name`,`p`.`car_model_id` AS `car_model_id`,`p`.`car_model_name` AS `car_model_name`,count(`p`.`spare_part_price_id`) AS `prices` from `sps_spp_cm2spp_cmo_v` `p` group by 1,2,3 order by 1,2;

-- --------------------------------------------------------

--
-- Structure for view `spp_simple_runs`
--
DROP VIEW IF EXISTS `spp_simple_runs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `spp_simple_runs` AS select count(0) AS `Rækker`,`spare_part_prices`.`spare_part_supplier_id` AS `spare_part_supplier_id`,`spare_part_prices`.`price_parser_run_id` AS `price_parser_run_id`,min(`spare_part_prices`.`created`) AS `started`,max(`spare_part_prices`.`created`) AS `latest` from `spare_part_prices` group by 2,3 with rollup;

-- --------------------------------------------------------

--
-- Structure for view `sps_spp_cm2spp_cmo_v`
--
DROP VIEW IF EXISTS `sps_spp_cm2spp_cmo_v`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `sps_spp_cm2spp_cmo_v` AS select `sps`.`supplier_name` AS `supplier_name`,`cmo`.`car_make_name` AS `car_make_name`,`cmo`.`car_model_name` AS `car_model_name`,`cmo`.`car_model_id` AS `car_model_id`,ifnull(`cmo`.`car_model_main_id`,`cmo`.`car_model_id`) AS `car_model_clean_id`,`spp`.`spare_part_price_id` AS `spare_part_price_id`,`spp`.`name` AS `name`,`spp`.`description` AS `description`,`spp`.`spare_part_url` AS `spare_part_url`,`spp`.`spare_part_image_url` AS `spare_part_image_url`,`spp`.`spare_part_category_id` AS `spare_part_category_id`,`spp`.`spare_part_category_free_text` AS `spare_part_category_free_text`,`spp`.`part_placement` AS `part_placement`,`spp`.`part_placement_left_right` AS `part_placement_left_right`,`spp`.`part_placement_front_back` AS `part_placement_front_back`,`spp`.`supplier_part_number` AS `supplier_part_number`,`spp`.`original_part_number` AS `original_part_number`,`spp`.`price_inc_vat` AS `price_inc_vat`,`spp`.`producer_make_name` AS `producer_make_name`,`spp`.`producer_part_number` AS `producer_part_number`,`spp`.`spare_part_supplier_id` AS `spare_part_supplier_id`,`cm2spp`.`year_from` AS `year_from`,`cm2spp`.`month_from` AS `month_from`,`cm2spp`.`year_to` AS `year_to`,`cm2spp`.`month_to` AS `month_to`,`cm2spp`.`chassis_no_from` AS `chassis_no_from`,`cm2spp`.`chassis_no_to` AS `chassis_no_to` from (((`spare_part_prices` `spp` join `spare_part_suppliers` `sps` on((`sps`.`spare_part_supplier_id` = `spp`.`spare_part_supplier_id`))) left join `car_models_to_spare_part_prices` `cm2spp` on((`spp`.`spare_part_price_id` = `cm2spp`.`spare_part_price_id`))) join `car_models_v` `cmo` on((`cm2spp`.`car_model_id` = `cmo`.`car_model_id`)));

-- --------------------------------------------------------

--
-- Structure for view `user_agent_grouped_by_agent_info`
--
DROP VIEW IF EXISTS `user_agent_grouped_by_agent_info`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `user_agent_grouped_by_agent_info` AS select `user_agents`.`user_agent_info` AS `user_agent_info`,count(1) AS `count`,min(`user_agents`.`created`) AS `FIRST`,max(`user_agents`.`created`) AS `last` from `user_agents` group by `user_agents`.`user_agent_info` having (count(1) > 1) order by 2 desc;

-- --------------------------------------------------------

--
-- Structure for view `user_agent_newest_with_age`
--
DROP VIEW IF EXISTS `user_agent_newest_with_age`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `user_agent_newest_with_age` AS select timestampdiff(MINUTE,`ua`.`created`,sysdate()) AS `age_mins`,timestampdiff(HOUR,`ua`.`created`,sysdate()) AS `age_hours`,timestampdiff(DAY,`ua`.`created`,sysdate()) AS `age_Days`,`ua`.`user_agent_id` AS `user_agent_id`,`ua`.`user_agent_info` AS `user_agent_info`,`ua`.`cookie_guid` AS `cookie_guid`,`ua`.`created` AS `created`,`ua`.`ip_addr_first_visit` AS `ip_addr_first_visit`,`ua`.`is_bot` AS `is_bot`,`ua`.`end_user_id` AS `end_user_id`,`ua`.`internal_note` AS `internal_note`,`ua`.`next_user_greting` AS `next_user_greting`,`ua`.`allow_remember_user_name` AS `allow_remember_user_name`,`ua`.`allow_automatic_login` AS `allow_automatic_login` from `user_agents` `ua` where ((not((`ua`.`user_agent_info` like _utf8'%bot%'))) and (not((`ua`.`user_agent_info` like _utf8'%spider%')))) order by `ua`.`created` desc;

-- --------------------------------------------------------

--
-- Structure for view `xml_http_request_jump_to`
--
DROP VIEW IF EXISTS `xml_http_request_jump_to`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `xml_http_request_jump_to` AS select substring_index(`xr`.`server_request_uri`,_utf8'/',-(1)) AS `spare_part_price_id`,`xr`.`xml_http_request_id` AS `xml_http_request_id`,`xr`.`created` AS `created` from (`xml_http_request` `xr` join `user_agent_types` `uat`) where ((`xr`.`server_request_uri` like _utf8'/index/jump_to%') and (`uat`.`user_agent_type_id` = `xr`.`user_agent_type_id`) and (`uat`.`is_bot` = 0) and (not(`xr`.`xml_http_request_id` in (select `jump_to_shop`.`xml_http_request_id` AS `xml_http_request_id` from `jump_to_shop` where (`jump_to_shop`.`xml_http_request_id` = `xr`.`xml_http_request_id`)))));

-- --------------------------------------------------------

--
-- Structure for view `xml_http_request_lim_10000`
--
DROP VIEW IF EXISTS `xml_http_request_lim_10000`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `xml_http_request_lim_10000` AS select `xml_http_request`.`xml_http_request_id` AS `xml_http_request_id`,`xml_http_request`.`created_by` AS `created_by`,`xml_http_request`.`request_payload` AS `request_payload`,`xml_http_request`.`updated` AS `updated`,`xml_http_request`.`created` AS `created`,`xml_http_request`.`first_response` AS `first_response`,`xml_http_request`.`latest_response` AS `latest_response`,`xml_http_request`.`server_request_uri` AS `server_request_uri`,`xml_http_request`.`unit_test_name` AS `unit_test_name`,`xml_http_request`.`unit_test_description` AS `unit_test_description`,`xml_http_request`.`user_info` AS `user_info`,`xml_http_request`.`client_ip` AS `client_ip`,`xml_http_request`.`user_agent` AS `user_agent`,`xml_http_request`.`http_cookie` AS `http_cookie`,`xml_http_request`.`request_time_taken_ms` AS `request_time_taken_ms`,`xml_http_request`.`data_rows_returned` AS `data_rows_returned`,`xml_http_request`.`sql_used` AS `sql_used`,`xml_http_request`.`q` AS `q`,`xml_http_request`.`car_model_id` AS `car_model_id`,`xml_http_request`.`car_make_id` AS `car_make_id`,`xml_http_request`.`trace` AS `trace`,`xml_http_request`.`http_referer` AS `http_referer`,`xml_http_request`.`user_agent_id` AS `user_agent_id`,`xml_http_request`.`user_agent_type_id` AS `user_agent_type_id` from `xml_http_request` limit 10000;

-- --------------------------------------------------------

--
-- Structure for view `xxcln_xhr_baibuspider`
--
DROP VIEW IF EXISTS `xxcln_xhr_baibuspider`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `xxcln_xhr_baibuspider` AS select `xml_http_request`.`xml_http_request_id` AS `xml_http_request_id`,`xml_http_request`.`user_agent` AS `user_agent`,`xml_http_request`.`created` AS `created` from `xml_http_request` where (`xml_http_request`.`user_agent` like _utf8'Moz%spi%') limit 10000;
 IF EXISTS `xml_http_request_jump_to`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `xml_http_request_jump_to` AS select substring_index(`xr`.`server_request_uri`,_utf8'/',-(1)) AS `spare_part_price_id`,`xr`.`xml_http_request_id` AS `xml_http_request_id`,`xr`.`created` AS `created` from (`xml_http_request` `xr` join `user_agent_types` `uat`) where ((`xr`.`server_request_uri` like _utf8'/index/jump_to%') and (`uat`.`user_agent_type_id` = `xr`.`user_agent_type_id`) and (`uat`.`is_bot` = 0) and (not(`xr`.`xml_http_request_id` in (select `jump_to_shop`.`xml_http_request_id` AS `xml_http_request_id` from `jump_to_shop` where (`jump_to_shop`.`xml_http_request_id` = `xr`.`xml_http_request_id`)))));

-- --------------------------------------------------------

--
-- Structure for view `xml_http_request_lim_10000`
--
DROP VIEW IF EXISTS `xml_http_request_lim_10000`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `xml_http_request_lim_10000` AS select `xml_http_request`.`xml_http_request_id` AS `xml_http_request_id`,`xml_http_request`.`created_by` AS `created_by`,`xml_http_request`.`request_payload` AS `request_payload`,`xml_http_request`.`updated` AS `updated`,`xml_http_request`.`created` AS `created`,`xml_http_request`.`first_response` AS `first_response`,`xml_http_request`.`latest_response` AS `latest_response`,`xml_http_request`.`server_request_uri` AS `server_request_uri`,`xml_http_request`.`unit_test_name` AS `unit_test_name`,`xml_http_request`.`unit_test_description` AS `unit_test_description`,`xml_http_request`.`user_info` AS `user_info`,`xml_http_request`.`client_ip` AS `client_ip`,`xml_http_request`.`user_agent` AS `user_agent`,`xml_http_request`.`http_cookie` AS `http_cookie`,`xml_http_request`.`request_time_taken_ms` AS `request_time_taken_ms`,`xml_http_request`.`data_rows_returned` AS `data_rows_returned`,`xml_http_request`.`sql_used` AS `sql_used`,`xml_http_request`.`q` AS `q`,`xml_http_request`.`car_model_id` AS `car_model_id`,`xml_http_request`.`car_make_id` AS `car_make_id`,`xml_http_request`.`trace` AS `trace`,`xml_http_request`.`http_referer` AS `http_referer`,`xml_http_request`.`user_agent_id` AS `user_agent_id`,`xml_http_request`.`user_agent_type_id` AS `user_agent_type_id` from `xml_http_request` limit 10000;

-- --------------------------------------------------------

--
-- Structure for view `xxcln_xhr_baibuspider`
--
DROP VIEW IF EXISTS `xxcln_xhr_baibuspider`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bdp_dev`@`localhost` SQL SECURITY DEFINER VIEW `xxcln_xhr_baibuspider` AS select `xml_http_request`.`xml_http_request_id` AS `xml_http_request_id`,`xml_http_request`.`user_agent` AS `user_agent`,`xml_http_request`.`created` AS `created` from `xml_http_request` where (`xml_http_request`.`user_agent` like _utf8'Moz%spi%') limit 10000;

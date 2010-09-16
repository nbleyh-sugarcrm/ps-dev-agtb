-- MSSQL upgrade script for Sugar 5.5.1 CE to 5.5.1 Ent

 ALTER TABLE users ADD default_team varchar(36)  NULL, team_set_id varchar(36) NULL ;
 create index idx_users_tmst_id  on users (team_set_id);


 ALTER TABLE leads ADD team_id varchar(36)  NULL ,
		       team_set_id varchar(36)  NULL ;

 ALTER TABLE contacts ADD team_id varchar(36)  NULL ,
			  team_set_id varchar(36)  NULL ,
			  portal_password varchar(32)  NULL ,
			  portal_name varchar(255) NULL default NULL,
			  portal_active bit NOT NULL default '0',
			  portal_app varchar(255) NULL default NULL;

 ALTER TABLE accounts ADD team_id varchar(36)  NULL ,
			  team_set_id varchar(36)  NULL ;


 ALTER TABLE opportunities ADD team_id varchar(36)  NULL ,
                               team_set_id varchar(36)  NULL ;

 ALTER TABLE cases ADD team_id varchar(36)  NULL ,
		       team_set_id varchar(36)  NULL ,
		       system_id int  NULL ,
		       portal_viewable bit  DEFAULT '0' NULL ;

DROP INDEX case_number on cases;
ALTER TABLE cases ADD CONSTRAINT case_number UNIQUE (case_number, system_id);

 ALTER TABLE notes ADD team_id varchar(36)  NULL ,
                       team_set_id varchar(36)  NULL ;

 ALTER TABLE email_templates ADD team_id varchar(36)  NULL ,
				team_set_id varchar(36)  NULL ,
				base_module varchar(50)  NULL ,
				from_name varchar(255)  NULL ,
				from_address varchar(255)  NULL ;

 ALTER TABLE calls ADD team_id varchar(36)  NULL ,
                       team_set_id varchar(36)  NULL ;

 ALTER TABLE emails ADD team_id varchar(36)  NULL ,
                        team_set_id varchar(36)  NULL ;

 ALTER TABLE meetings ADD team_id varchar(36)  NULL ,
                          team_set_id varchar(36)  NULL ;

 ALTER TABLE tasks ADD team_id varchar(36)  NULL ,
                       team_set_id varchar(36)  NULL ;

 ALTER TABLE tracker ADD team_id varchar(36)  NULL ;


CREATE TABLE tracker_perf (id int  NOT NULL identity(1,1),monitor_id varchar(36)  NOT NULL ,server_response_time float  NULL ,db_round_trips int  NULL ,files_opened int  NULL ,memory_usage int  NULL ,deleted bit  DEFAULT '0' NULL ,date_modified datetime  NULL  ) ALTER TABLE tracker_perf ADD CONSTRAINT pk_tracker_perf PRIMARY KEY (id) create index idx_tracker_perf_mon_id on tracker_perf ( monitor_id );

CREATE TABLE tracker_sessions (id int  NOT NULL identity(1,1),session_id varchar(36)  NULL ,date_start datetime  NULL ,date_end datetime  NULL ,seconds int  DEFAULT '0' NULL ,client_ip varchar(20)  NULL ,user_id varchar(36)  NULL ,active bit  DEFAULT '1' NULL ,round_trips int  NULL ,deleted bit  DEFAULT '0' NULL  ) ALTER TABLE tracker_sessions ADD CONSTRAINT pk_tracker_sessions PRIMARY KEY (id) create index idx_tracker_sessions_s_id on tracker_sessions ( session_id ) create index idx_tracker_sessions_uas_id on tracker_sessions ( user_id, active, session_id );

CREATE TABLE tracker_queries (id int  NOT NULL identity(1,1),query_id varchar(36)  NOT NULL ,text text  NULL ,query_hash varchar(36)  NULL ,sec_total float  NULL ,sec_avg float  NULL ,run_count int  NULL ,deleted bit  DEFAULT '0' NULL ,date_modified datetime  NULL  ) ALTER TABLE tracker_queries ADD CONSTRAINT pk_tracker_queries PRIMARY KEY (id) create index idx_tracker_queries_query_hash on tracker_queries ( query_hash ) create index idx_tracker_queries_query_id on tracker_queries ( query_id );


 ALTER TABLE bugs ADD team_id varchar(36)  NULL ,
		      team_set_id varchar(36)  NULL ,
		      system_id int  NULL ,
		      portal_viewable bit  DEFAULT '0' NULL ;

DROP INDEX bug_number on bugs;
ALTER TABLE bugs ADD CONSTRAINT bug_number UNIQUE (bug_number, system_id);

 ALTER TABLE project ADD team_id varchar(36)  NULL ,
			team_set_id varchar(36)  NULL ,
			is_template bit  DEFAULT '0' NULL ;

 ALTER TABLE project_task ADD team_id varchar(36)  NULL ,
			 team_set_id varchar(36)  NULL ,
			 resource_id text  NULL ;

 ALTER TABLE campaigns ADD team_id varchar(36)  NULL ,
			team_set_id varchar(36)  NULL ;

 ALTER TABLE prospect_lists ADD team_id varchar(36)  NULL ,
				team_set_id varchar(36)  NULL ;

 ALTER TABLE prospects ADD team_id varchar(36)  NULL ,
			team_set_id varchar(36)  NULL ;

 ALTER TABLE documents ADD team_id varchar(36)  NULL ,
			team_set_id varchar(36)  NULL ;

 ALTER TABLE inbound_email ADD team_id varchar(36)  NULL ,
				team_set_id varchar(36)  NULL ;

 ALTER TABLE saved_search ADD team_id varchar(36)  NULL ,
			team_set_id varchar(36)  NULL ;


CREATE TABLE acl_fields (id varchar(36)  NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NOT NULL ,created_by varchar(36)  NULL ,name varchar(150)  NULL ,category varchar(100)  NULL ,aclaccess int  NULL ,deleted bit  DEFAULT 0  NULL ,role_id varchar(36)  NOT NULL  ) ALTER TABLE acl_fields ADD CONSTRAINT pk_acl_fields PRIMARY KEY (id) create index idx_aclfield_role_del on acl_fields ( role_id, category, deleted );


CREATE TABLE contracts (id varchar(36)  NOT NULL ,name varchar(255)  NOT NULL ,date_entered datetime  NULL ,date_modified datetime  NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,description text  NULL ,deleted bit  DEFAULT '0' NULL ,assigned_user_id varchar(36)  NULL ,team_id varchar(36)  NULL ,team_set_id varchar(36)  NULL ,reference_code varchar(255)  NULL ,account_id varchar(36)  NULL ,start_date datetime  NULL ,end_date datetime  NULL ,currency_id varchar(36)  NULL ,total_contract_value decimal(26,6)  NULL ,total_contract_value_usdollar decimal(26,6)  NULL ,status varchar(25)  NOT NULL ,customer_signed_date datetime  NULL ,company_signed_date datetime  NULL ,expiration_notice datetime  NULL ,type varchar(255)  NULL  ) ALTER TABLE contracts ADD CONSTRAINT pk_contracts PRIMARY KEY (id);

CREATE TABLE contracts_audit (
    id varchar(36) NOT NULL DEFAULT '' ,
    parent_id varchar(36) NOT NULL DEFAULT '' ,
    date_created datetime NULL DEFAULT NULL ,
    created_by varchar(36) NULL DEFAULT NULL ,
    field_name varchar(100) NULL DEFAULT NULL ,
    data_type varchar(100) NULL DEFAULT NULL ,
    before_value_string varchar(255) NULL DEFAULT NULL ,
    after_value_string varchar(255) NULL DEFAULT NULL ,
    before_value_text text NULL DEFAULT NULL ,
    after_value_text text NULL DEFAULT NULL 
) ;

CREATE TABLE saved_reports (team_id varchar(36)  NULL ,team_set_id varchar(36)  NULL ,id varchar(36)  NOT NULL ,name varchar(255)  NOT NULL ,module varchar(36)  NOT NULL ,report_type varchar(36)  NOT NULL ,content text  NULL ,deleted bit  DEFAULT 0  NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,assigned_user_id varchar(36)  NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,is_published bit  DEFAULT '0' NOT NULL ,chart_type varchar(36)  DEFAULT 'none' NOT NULL ,schedule_type varchar(3)  DEFAULT 'pro' NULL ,favorite bit  DEFAULT 0  NULL  ) ALTER TABLE saved_reports ADD CONSTRAINT pk_saved_reports PRIMARY KEY (id) create index idx_rep_owner_module_name on saved_reports ( assigned_user_id, name, deleted );


CREATE TABLE teams (id varchar(36)  NOT NULL ,name varchar(128)  NULL ,name_2 varchar(128)  NULL ,associated_user_id varchar(36)  NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,private bit  DEFAULT 0  NULL ,description text  NULL ,deleted bit  DEFAULT 0  NOT NULL  ) ALTER TABLE teams ADD CONSTRAINT pk_teams PRIMARY KEY (id) create index idx_team_del on teams ( name ) create index idx_team_del_name on teams ( deleted, name );


CREATE TABLE team_memberships (id varchar(36)  NOT NULL ,team_id varchar(36)  NULL ,user_id varchar(36)  NULL ,explicit_assign bit  DEFAULT '0' NOT NULL ,implicit_assign bit  DEFAULT '0' NOT NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NULL  ) ALTER TABLE team_memberships ADD CONSTRAINT pk_team_memberships PRIMARY KEY (id) create index idx_team_membership on team_memberships ( user_id, team_id ) create index idx_teammemb_team_user on team_memberships ( team_id, user_id );


CREATE TABLE team_sets (id varchar(36)  NOT NULL ,name varchar(128)  NULL ,team_md5 varchar(32)  NULL ,team_count int  DEFAULT '0' NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NULL ,created_by varchar(36)  NULL  ) ALTER TABLE team_sets ADD CONSTRAINT pk_team_sets PRIMARY KEY (id) create index idx_team_sets_md5 on team_sets ( team_md5 );


CREATE TABLE team_sets_modules (id varchar(36)  NOT NULL ,team_set_id varchar(36)  NULL ,module_table_name varchar(128)  NULL ,deleted bit  DEFAULT '0' NULL  ) ALTER TABLE team_sets_modules ADD CONSTRAINT pk_team_sets_modules PRIMARY KEY (id) create index idx_team_sets_modules on team_sets_modules ( team_set_id );


CREATE TABLE team_notices (team_id varchar(36)  NULL ,team_set_id varchar(36)  NULL ,id varchar(36)  NOT NULL ,deleted bit  DEFAULT 0  NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,date_start datetime  NOT NULL ,date_end datetime  NOT NULL ,modified_user_id varchar(36)  NOT NULL ,created_by varchar(36)  NULL ,name varchar(50)  NOT NULL ,description text  NULL ,status varchar(25)  NULL ,url varchar(255)  NULL ,url_title varchar(255)  NULL  ) ALTER TABLE team_notices ADD CONSTRAINT pk_team_notices PRIMARY KEY (id) create index idx_team_notice on team_notices ( name, deleted );


CREATE TABLE product_templates (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,type_id varchar(36)  NULL ,manufacturer_id varchar(36)  NULL ,category_id varchar(36)  NULL ,name varchar(50)  NULL ,mft_part_num varchar(50)  NULL ,vendor_part_num varchar(50)  NULL ,date_cost_price datetime  NULL ,cost_price decimal(26,6)  NOT NULL ,discount_price decimal(26,6)  NOT NULL ,list_price decimal(26,6)  NOT NULL ,cost_usdollar decimal(26,6)  NULL ,discount_usdollar decimal(26,6)  NULL ,list_usdollar decimal(26,6)  NULL ,currency_id varchar(36)  NULL ,currency varchar(255)  NULL ,status varchar(25)  NULL ,tax_class varchar(25)  NULL ,date_available datetime  NULL ,website varchar(255)  NULL ,weight decimal(12,2)  NULL ,qty_in_stock int  NULL ,description text  NULL ,support_name varchar(50)  NULL ,support_description varchar(255)  NULL ,support_contact varchar(50)  NULL ,support_term varchar(25)  NULL ,pricing_formula varchar(25)  NULL ,pricing_factor int  NULL  ) ALTER TABLE product_templates ADD CONSTRAINT pk_product_templates PRIMARY KEY (id) create index idx_product_template on product_templates ( name, deleted );


CREATE TABLE product_types (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,name varchar(50)  NULL ,description text  NULL ,list_order int  NULL  ) ALTER TABLE product_types ADD CONSTRAINT pk_product_types PRIMARY KEY (id) create index idx_producttypes on product_types ( name, deleted );


CREATE TABLE product_categories (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,name varchar(50)  NULL ,list_order int  NULL ,description text  NULL ,parent_id varchar(36)  NULL  ) ALTER TABLE product_categories ADD CONSTRAINT pk_product_categories PRIMARY KEY (id) create index idx_productcategories on product_categories ( name, deleted );


CREATE TABLE manufacturers (id varchar(36)  NOT NULL ,deleted bit  DEFAULT 0  NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NOT NULL ,created_by varchar(36)  NULL ,name varchar(50)  NOT NULL ,list_order int  NULL ,status varchar(25)  NULL  ) ALTER TABLE manufacturers ADD CONSTRAINT pk_manufacturers PRIMARY KEY (id) create index idx_manufacturers on manufacturers ( name, deleted );


CREATE TABLE quotes (id varchar(36)  NOT NULL ,name varchar(50)  NULL ,date_entered datetime  NULL ,date_modified datetime  NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,description text  NULL ,deleted bit  DEFAULT '0' NULL ,assigned_user_id varchar(36)  NULL ,team_id varchar(36)  NULL ,team_set_id varchar(36)  NULL ,shipper_id varchar(36)  NULL ,currency_id varchar(36)  NULL ,taxrate_id varchar(36)  NULL ,show_line_nums bit  DEFAULT '1' NOT NULL ,calc_grand_total bit  DEFAULT '1' NOT NULL ,quote_type varchar(25)  NULL ,date_quote_expected_closed datetime  NULL ,original_po_date datetime  NULL ,payment_terms varchar(128)  NULL ,date_quote_closed datetime  NULL ,date_order_shipped datetime  NULL ,order_stage varchar(25)  NULL ,quote_stage varchar(25)  NULL ,purchase_order_num varchar(50)  NULL ,quote_num int  NOT NULL identity(1,1),subtotal decimal(26,6)  NULL ,subtotal_usdollar decimal(26,6)  NULL ,shipping decimal(26,6)  NULL ,shipping_usdollar decimal(26,6)  NULL ,discount decimal(26,6)  NULL ,deal_tot decimal(26,2)  NULL ,deal_tot_usdollar decimal(26,2)  NULL ,new_sub decimal(26,6)  NULL ,new_sub_usdollar decimal(26,6)  NULL ,tax decimal(26,6)  NULL ,tax_usdollar decimal(26,6)  NULL ,total decimal(26,6)  NULL ,total_usdollar decimal(26,6)  NULL ,billing_address_street varchar(150)  NULL ,billing_address_city varchar(100)  NULL ,billing_address_state varchar(100)  NULL ,billing_address_postalcode varchar(20)  NULL ,billing_address_country varchar(100)  NULL ,shipping_address_street varchar(150)  NULL ,shipping_address_city varchar(100)  NULL ,shipping_address_state varchar(100)  NULL ,shipping_address_postalcode varchar(20)  NULL ,shipping_address_country varchar(100)  NULL ,system_id int  NULL  ) ALTER TABLE quotes ADD CONSTRAINT pk_quotes PRIMARY KEY (id) ALTER TABLE quotes ADD CONSTRAINT quote_num UNIQUE (quote_num, system_id) create index idx_qte_name on quotes ( name );


CREATE TABLE product_bundle_notes (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,description text  NULL  ) ALTER TABLE product_bundle_notes ADD CONSTRAINT pk_product_bundle_notes PRIMARY KEY (id);


CREATE TABLE products (id varchar(36)  NOT NULL ,name varchar(50)  NULL ,date_entered datetime  NULL ,date_modified datetime  NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,description text  NULL ,deleted bit  DEFAULT '0' NULL ,team_id varchar(36)  NULL ,team_set_id varchar(36)  NULL ,product_template_id varchar(36)  NULL ,account_id varchar(36)  NULL ,contact_id varchar(36)  NULL ,type_id varchar(36)  NULL ,quote_id varchar(36)  NULL ,manufacturer_id varchar(36)  NULL ,category_id varchar(36)  NULL ,mft_part_num varchar(50)  NULL ,vendor_part_num varchar(50)  NULL ,date_purchased datetime  NULL ,cost_price decimal(26,6)  NULL ,discount_price decimal(26,6)  NULL ,discount_amount decimal(26,6)  NULL ,discount_select bit  DEFAULT 0  NULL,deal_calc decimal(26,6)  NULL ,deal_calc_usdollar decimal(26,6)  NULL ,discount_amount_usdollar decimal(26,6) default NULL,list_price decimal(26,6)  NULL ,cost_usdollar decimal(26,6)  NULL ,discount_usdollar decimal(26,6)  NULL ,list_usdollar decimal(26,6)  NULL ,currency_id varchar(36)  NULL ,status varchar(25)  NULL ,tax_class varchar(25)  NULL ,website varchar(255)  NULL ,weight decimal(12,2)  NULL ,quantity int  NULL ,support_name varchar(50)  NULL ,support_description varchar(255)  NULL ,support_contact varchar(50)  NULL ,support_term varchar(25)  NULL ,date_support_expires datetime  NULL ,date_support_starts datetime  NULL ,pricing_formula varchar(25)  NULL ,pricing_factor int  NULL ,serial_number varchar(50)  NULL ,asset_number varchar(50)  NULL ,book_value decimal(26,6)  NULL ,book_value_date datetime  NULL  ) ALTER TABLE products ADD CONSTRAINT pk_products PRIMARY KEY (id) create index idx_products on products ( name, deleted );

CREATE TABLE products_audit (
    id varchar(36) NOT NULL DEFAULT '' ,
    parent_id varchar(36) NOT NULL DEFAULT '' ,
    date_created datetime NULL DEFAULT NULL ,
    created_by varchar(36) NULL DEFAULT NULL ,
    field_name varchar(100) NULL DEFAULT NULL ,
    data_type varchar(100) NULL DEFAULT NULL ,
    before_value_string varchar(255) NULL DEFAULT NULL ,
    after_value_string varchar(255) NULL DEFAULT NULL ,
    before_value_text text NULL DEFAULT NULL ,
    after_value_text text NULL DEFAULT NULL 
);

CREATE TABLE product_bundles (team_id varchar(36)  NULL ,team_set_id varchar(36)  NULL ,id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,name varchar(255)  NULL ,bundle_stage varchar(255)  NULL ,description text  NULL ,tax decimal(26,6)  NULL ,tax_usdollar decimal(26,6)  NULL ,total decimal(26,6)  NULL ,total_usdollar decimal(26,6)  NULL ,subtotal_usdollar decimal(26,6)  NULL ,shipping_usdollar decimal(26,6)  NULL ,deal_tot decimal(26,2)  NULL ,deal_tot_usdollar decimal(26,2)  NULL ,new_sub decimal(26,6)  NULL ,new_sub_usdollar decimal(26,6)  NULL ,subtotal decimal(26,6)  NULL ,shipping decimal(26,6)  NULL ,currency_id varchar(36)  NULL  ) ALTER TABLE product_bundles ADD CONSTRAINT pk_product_bundles PRIMARY KEY (id) create index idx_products_bundles on product_bundles ( name, deleted );


CREATE TABLE shippers (id varchar(36)  NOT NULL ,deleted bit  DEFAULT 0  NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NOT NULL ,created_by varchar(36)  NULL ,name varchar(50)  NOT NULL ,list_order int  NULL ,status varchar(25)  NULL  ) ALTER TABLE shippers ADD CONSTRAINT pk_shippers PRIMARY KEY (id) create index idx_shippers on shippers ( name, deleted );


CREATE TABLE taxrates (id varchar(36)  NOT NULL ,deleted bit  DEFAULT 0  NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NOT NULL ,created_by varchar(36)  NULL ,name varchar(50)  NOT NULL ,value decimal(7,5)  NULL ,list_order int  NULL ,status varchar(25)  NULL  ) ALTER TABLE taxrates ADD CONSTRAINT pk_taxrates PRIMARY KEY (id) create index idx_taxrates on taxrates ( name, deleted );


CREATE TABLE timeperiods (id varchar(36)  NOT NULL ,name varchar(36)  NULL ,parent_id varchar(36)  NULL ,start_date datetime  NULL ,end_date datetime  NULL ,created_by varchar(36)  NULL ,date_entered datetime  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT 0  NULL ,is_fiscal_year bit  DEFAULT 0  NULL  ) ALTER TABLE timeperiods ADD CONSTRAINT pk_timeperiods PRIMARY KEY (id);


CREATE TABLE forecasts (id varchar(36)  NOT NULL ,timeperiod_id varchar(36)  NULL ,forecast_type varchar(25)  NULL ,opp_count int  NULL ,opp_weigh_value int  NULL ,best_case int  NULL ,likely_case int  NULL ,worst_case int  NULL ,user_id varchar(36)  NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,deleted bit  DEFAULT 0  NOT NULL  ) ALTER TABLE forecasts ADD CONSTRAINT pk_forecasts PRIMARY KEY (id);


CREATE TABLE forecast_schedule (id varchar(36)  NOT NULL ,timeperiod_id varchar(36)  NULL ,user_id varchar(36)  NULL ,cascade_hierarchy bit  DEFAULT 0  NULL ,forecast_start_date datetime  NULL ,status varchar(25)  NULL ,created_by varchar(36)  NULL ,date_entered datetime  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT 0  NULL  ) ALTER TABLE forecast_schedule ADD CONSTRAINT pk_forecast_schedule PRIMARY KEY (id);


CREATE TABLE quotas (id varchar(36)  NOT NULL ,user_id varchar(36)  NOT NULL ,timeperiod_id varchar(36)  NOT NULL ,quota_type varchar(25)  NULL ,amount int  NOT NULL ,amount_base_currency int  NOT NULL ,currency_id varchar(36)  NOT NULL ,committed bit  DEFAULT '0' NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,date_entered datetime  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT 0  NULL  ) ALTER TABLE quotas ADD CONSTRAINT pk_quotas PRIMARY KEY (id);


CREATE TABLE worksheet (id varchar(36)  NOT NULL ,user_id varchar(36)  NULL ,timeperiod_id varchar(36)  NULL ,forecast_type varchar(25)  NULL ,related_id varchar(36)  NULL ,related_forecast_type varchar(25)  NULL ,best_case int  NULL ,likely_case int  NULL ,worst_case int  NULL ,date_modified datetime  NULL ,modified_user_id varchar(36)  NULL  ) ALTER TABLE worksheet ADD CONSTRAINT pk_worksheet PRIMARY KEY (id);


CREATE TABLE workflow (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,name varchar(50)  NULL ,base_module varchar(50)  NULL ,status bit  DEFAULT '0' NULL ,description text  NULL ,type varchar(25)  NOT NULL ,fire_order varchar(25)  NOT NULL ,parent_id varchar(36)  NULL ,record_type varchar(25)  NOT NULL ,list_order_y int  DEFAULT '0' NULL  ) ALTER TABLE workflow ADD CONSTRAINT pk_workflow PRIMARY KEY (id) create index idx_workflow on workflow ( name, deleted );


CREATE TABLE workflow_triggershells (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,field varchar(50)  NULL ,type varchar(25)  NOT NULL ,frame_type varchar(15)  NOT NULL ,eval text  NULL ,parent_id varchar(36)  NOT NULL ,show_past bit  DEFAULT '0' NULL ,rel_module varchar(50)  NULL ,rel_module_type varchar(10)  NULL ,parameters varchar(255)  NULL  ) ALTER TABLE workflow_triggershells ADD CONSTRAINT pk_workflow_triggershells PRIMARY KEY (id);


CREATE TABLE workflow_alertshells (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,name varchar(50)  NULL ,alert_text text  NULL ,alert_type varchar(25)  NOT NULL ,source_type varchar(25)  NOT NULL ,parent_id varchar(36)  NOT NULL ,custom_template_id varchar(36)  NULL  ) ALTER TABLE workflow_alertshells ADD CONSTRAINT pk_workflow_alertshells PRIMARY KEY (id) create index idx_workflowalertshell on workflow_alertshells ( name, deleted );


CREATE TABLE workflow_alerts (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,field_value varchar(50)  NULL ,rel_email_value varchar(50)  NULL ,rel_module1 varchar(50)  NULL ,rel_module2 varchar(50)  NULL ,rel_module1_type varchar(10)  NULL ,rel_module2_type varchar(10)  NULL ,where_filter bit  DEFAULT '0' NULL ,user_type varchar(25)  NOT NULL ,array_type varchar(25)  NULL ,relate_type varchar(25)  NULL ,address_type varchar(25)  NULL ,parent_id varchar(36)  NOT NULL ,user_display_type varchar(25)  NULL  ) ALTER TABLE workflow_alerts ADD CONSTRAINT pk_workflow_alerts PRIMARY KEY (id) create index idx_workflowalerts on workflow_alerts ( deleted );


CREATE TABLE workflow_actionshells (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,action_type varchar(25)  NOT NULL ,parent_id varchar(36)  NOT NULL ,parameters varchar(255)  NULL ,rel_module varchar(50)  NULL ,rel_module_type varchar(10)  NULL ,action_module varchar(50)  NULL  ) ALTER TABLE workflow_actionshells ADD CONSTRAINT pk_workflow_actionshells PRIMARY KEY (id) create index idx_actionshell on workflow_actionshells ( deleted );


CREATE TABLE workflow_actions (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,field varchar(50)  NULL ,value text  NULL ,set_type varchar(10)  NOT NULL ,adv_type varchar(10)  NULL ,parent_id varchar(36)  NOT NULL ,ext1 varchar(50)  NULL ,ext2 varchar(50)  NULL ,ext3 varchar(50)  NULL  ) ALTER TABLE workflow_actions ADD CONSTRAINT pk_workflow_actions PRIMARY KEY (id) create index idx_action on workflow_actions ( deleted );


CREATE TABLE expressions (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,lhs_type varchar(15)  NULL ,lhs_field varchar(50)  NULL ,lhs_module varchar(50)  NULL ,lhs_value varchar(100)  NULL ,lhs_group_type varchar(10)  NULL ,operator varchar(15)  NULL ,rhs_group_type varchar(10)  NULL ,rhs_type varchar(15)  NULL ,rhs_field varchar(50)  NULL ,rhs_module varchar(50)  NULL ,rhs_value varchar(255)  NULL ,parent_id varchar(36)  NOT NULL ,exp_type varchar(25)  NOT NULL ,exp_order int  NULL ,parent_type varchar(25)  NULL ,parent_exp_id varchar(36)  NULL ,parent_exp_side int  NULL ,ext1 varchar(50)  NULL ,ext2 varchar(50)  NULL ,ext3 varchar(50)  NULL  ) ALTER TABLE expressions ADD CONSTRAINT pk_expressions PRIMARY KEY (id) create index idx_exp on expressions ( parent_id, deleted );


CREATE TABLE systems (id varchar(36)  NOT NULL ,system_id int  NOT NULL identity(1,1),system_key varchar(36)  NULL ,user_id varchar(36)  NULL ,last_connect_date datetime  NULL ,status varchar(255)  DEFAULT 'Active' NULL ,num_syncs int  DEFAULT '0' NULL ,system_name varchar(100)  NULL ,install_method varchar(100)  NULL ,date_entered datetime  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE systems ADD CONSTRAINT pk_systems PRIMARY KEY (id) create index system_id on systems ( system_id );


CREATE TABLE session_active (id varchar(36)  NOT NULL ,session_id varchar(100)  NULL ,last_request_time datetime  NULL ,session_type varchar(100)  NULL ,is_violation bit  DEFAULT '0' NULL ,num_active_sessions int  DEFAULT '0' NULL ,date_entered datetime  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE session_active ADD CONSTRAINT pk_session_active PRIMARY KEY (id) ALTER TABLE session_active ADD CONSTRAINT idx_session_id UNIQUE (session_id);


CREATE TABLE kbdocuments (team_id varchar(36)  NULL ,team_set_id varchar(36)  NULL ,id varchar(36)  NOT NULL ,kbdocument_name varchar(255)  NOT NULL ,active_date datetime  NULL ,exp_date datetime  NULL ,status_id varchar(25)  NULL ,date_entered datetime  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NULL ,is_external_article bit  DEFAULT '0' NULL ,description text  NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,kbdocument_revision_id varchar(36)  NULL ,kbdocument_revision_number varchar(25)  NULL ,mail_merge_document varchar(3)  DEFAULT 'off' NULL ,related_doc_id varchar(36)  NULL ,related_doc_rev_id varchar(36)  NULL ,is_template bit  DEFAULT '0' NULL ,template_type varchar(25)  NULL ,kbdoc_approver_id varchar(36)  NULL ,assigned_user_id varchar(36)  NULL ,parent_id varchar(36)  NULL ,parent_type varchar(25)  NULL  ) ALTER TABLE kbdocuments ADD CONSTRAINT pk_kbdocuments PRIMARY KEY (id);


CREATE TABLE kbdocument_revisions (id varchar(36)  NOT NULL ,change_log varchar(255)  NULL ,kbdocument_id varchar(36)  NULL ,date_entered datetime  NULL ,created_by varchar(36)  NULL ,filename varchar(255)  NULL ,file_ext varchar(25)  NULL ,file_mime_type varchar(100)  NULL ,revision varchar(25)  NULL ,deleted bit  DEFAULT '0' NULL ,latest bit  DEFAULT '0' NULL ,kbcontent_id varchar(36)  NULL ,document_revision_id varchar(36)  NULL ,date_modified datetime  NULL  )  
ALTER TABLE kbdocument_revisions ADD CONSTRAINT pk_kbdocument_revisions PRIMARY KEY (id) 
create index idx_del_latest_kbcontent_id on kbdocument_revisions ( deleted, latest, kbcontent_id ) 
create index idx_cont_id_doc_id on kbdocument_revisions ( kbcontent_id, kbdocument_id ) 
create index idx_name_rev_id_del on kbdocument_revisions ( document_revision_id, kbdocument_id, deleted );


CREATE TABLE kbtags (team_id varchar(36)  NULL ,team_set_id varchar(36)  NULL ,id varchar(36)  NOT NULL ,parent_tag_id varchar(36)  NULL ,tag_name varchar(255)  NOT NULL ,root_tag bit  DEFAULT '0' NULL ,date_entered datetime  NULL ,created_by varchar(36)  NULL ,revision varchar(25)  NULL ,deleted bit  DEFAULT '0' NULL ,date_modified datetime  NULL  ) ALTER TABLE kbtags ADD CONSTRAINT pk_kbtags PRIMARY KEY (id);


CREATE TABLE kbdocuments_kbtags (team_id varchar(36)  NULL ,team_set_id varchar(36)  NULL ,id varchar(36)  NOT NULL ,kbdocument_id varchar(36)  NULL ,kbtag_id varchar(36)  NULL ,date_entered datetime  NULL ,created_by varchar(36)  NULL ,revision varchar(25)  NULL ,deleted bit  DEFAULT '0' NULL ,date_modified datetime  NULL  )  
ALTER TABLE kbdocuments_kbtags ADD CONSTRAINT pk_kbdocuments_kbtags PRIMARY KEY (id) 
create index idx_doc_id_tag_id on kbdocuments_kbtags ( kbdocument_id, kbtag_id );

CREATE TABLE kbcontents (team_id varchar(36)  NULL ,team_set_id varchar(36)  NULL ,id varchar(36)  NOT NULL ,kbdocument_body text  NULL ,document_revision_id varchar(36)  NULL ,date_entered datetime  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NULL ,modified_user_id varchar(36)  NULL ,kb_index int  NOT NULL identity(1,1) ) ALTER TABLE kbcontents ADD CONSTRAINT pk_kbcontents PRIMARY KEY (id) ALTER TABLE kbcontents ADD CONSTRAINT fts_unique_idx UNIQUE (kb_index) ; 

CREATE TABLE kbcontents_audit (
    id varchar(36) NOT NULL DEFAULT '' ,
    parent_id varchar(36) NOT NULL DEFAULT '' ,
    date_created datetime NULL DEFAULT NULL ,
    created_by varchar(36) NULL DEFAULT NULL ,
    field_name varchar(100) NULL DEFAULT NULL ,
    data_type varchar(100) NULL DEFAULT NULL ,
    before_value_string varchar(255) NULL DEFAULT NULL ,
    after_value_string varchar(255) NULL DEFAULT NULL ,
    before_value_text text NULL DEFAULT NULL ,
    after_value_text text NULL DEFAULT NULL 
) ;

CREATE TABLE contract_types (id varchar(36)  NOT NULL ,name varchar(30)  NULL ,list_order int  NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE contract_types ADD CONSTRAINT pk_contract_types PRIMARY KEY (id);


CREATE TABLE project_resources (id varchar(36)  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,project_id varchar(36)  NULL ,resource_id varchar(36)  NULL ,resource_type varchar(20)  NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE project_resources ADD CONSTRAINT pk_project_resources PRIMARY KEY (id);


CREATE TABLE holidays (id varchar(36)  NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NOT NULL ,created_by varchar(36)  NULL ,holiday_date datetime  NOT NULL ,description text  NULL ,deleted bit  DEFAULT 0  NULL ,person_id varchar(36)  NULL ,person_type varchar(255)  NULL ,related_module varchar(255)  NULL ,related_module_id varchar(36)  NULL ,resource_name varchar(255)  NULL  ) ALTER TABLE holidays ADD CONSTRAINT pk_holidays PRIMARY KEY (id) create index idx_holiday_id_del on holidays ( id, deleted );


CREATE TABLE custom_queries (team_id varchar(36)  NULL ,team_set_id varchar(36)  NULL ,id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,name varchar(50)  NULL ,description text  NULL ,custom_query text  NULL ,query_type varchar(50)  NULL ,list_order int  NULL ,query_locked varchar(3)  DEFAULT '0' NULL  ) ALTER TABLE custom_queries ADD CONSTRAINT pk_custom_queries PRIMARY KEY (id) create index idx_customqueries on custom_queries ( name, deleted );


CREATE TABLE data_sets (team_id varchar(36)  NULL ,team_set_id varchar(36)  NULL ,id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,parent_id varchar(36)  NULL ,report_id varchar(36)  NULL ,query_id varchar(36)  NOT NULL ,name varchar(50)  NULL ,list_order_y int  DEFAULT '0' NULL ,exportable varchar(3)  DEFAULT '0' NULL ,header varchar(3)  DEFAULT '0' NULL ,description text  NULL ,table_width varchar(3)  DEFAULT '0' NULL ,font_size varchar(8)  DEFAULT '0' NULL ,output_default varchar(25)  NULL ,prespace_y varchar(3)  DEFAULT '0' NULL ,use_prev_header varchar(3)  DEFAULT '0' NULL ,header_back_color varchar(25)  NULL ,body_back_color varchar(25)  NULL ,header_text_color varchar(25)  NULL ,body_text_color varchar(25)  NULL ,table_width_type varchar(3)  NULL ,custom_layout varchar(10)  NULL  ) ALTER TABLE data_sets ADD CONSTRAINT pk_data_sets PRIMARY KEY (id) create index idx_dataset on data_sets ( name, deleted );


CREATE TABLE report_maker (team_id varchar(36)  NULL ,team_set_id varchar(36)  NULL ,id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,name varchar(50)  NULL ,title varchar(50)  NULL ,report_align varchar(8)  NULL ,description text  NULL ,scheduled bit  DEFAULT '0' NULL  ) ALTER TABLE report_maker ADD CONSTRAINT pk_report_maker PRIMARY KEY (id) create index idx_rmaker on report_maker ( name, deleted );



 ALTER TABLE sugarfeed ADD team_id varchar(36)  NULL ,
			team_set_id varchar(36)  NULL ;


 ALTER TABLE folders ADD team_set_id varchar(36)  NOT NULL ,
			team_id varchar(36)  NOT NULL ;


CREATE TABLE team_sets_teams (id varchar(36)  NOT NULL ,team_set_id varchar(36)  NULL ,team_id varchar(36)  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NULL  ) create index idx_ud_set_id on team_sets_teams ( team_set_id, team_id ) create index idx_ud_team_id on team_sets_teams ( team_id ) create index idx_ud_team_set_id on team_sets_teams (team_set_id);


CREATE TABLE tracker_tracker_queries (id int  NOT NULL identity(1,1),monitor_id varchar(36)  NULL ,query_id varchar(36)  NULL ,date_modified datetime  NULL  ) ALTER TABLE tracker_tracker_queries ADD CONSTRAINT pk_tracker_tracker_queries PRIMARY KEY (id) create index idx_tracker_tq_monitor on tracker_tracker_queries ( monitor_id ) create index idx_tracker_tq_query on tracker_tracker_queries ( query_id );


CREATE TABLE address_book_lists (id varchar(36)  NOT NULL ,assigned_user_id varchar(36)  NOT NULL ,list_name varchar(100)  NOT NULL  ) ALTER TABLE address_book_lists ADD CONSTRAINT pk_address_book_lists PRIMARY KEY (id) create index abml_user_bean_idx on address_book_lists ( assigned_user_id );


CREATE TABLE address_book_list_items (list_id varchar(36)  NOT NULL ,bean_id varchar(36)  NOT NULL  ) create index abli_list_id_idx on address_book_list_items ( list_id ) create index abli_list_id_bean_idx on address_book_list_items ( list_id, bean_id );


CREATE TABLE product_bundle_note (id varchar(36)  NOT NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL ,bundle_id varchar(36)  NULL ,note_id varchar(36)  NULL ,note_index int  DEFAULT '0' NOT NULL  ) ALTER TABLE product_bundle_note ADD CONSTRAINT pk_product_bundle_note PRIMARY KEY (id) create index idx_pbn_bundle on product_bundle_note ( bundle_id ) create index idx_pbn_note on product_bundle_note ( note_id ) create index idx_pbn_pb_nb on product_bundle_note ( note_id, bundle_id );


CREATE TABLE product_bundle_product (id varchar(36)  NOT NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL ,bundle_id varchar(36)  NULL ,product_id varchar(36)  NULL ,product_index int  DEFAULT '0' NOT NULL  ) ALTER TABLE product_bundle_product ADD CONSTRAINT pk_product_bundle_product PRIMARY KEY (id) create index idx_pbp_bundle on product_bundle_product ( bundle_id ) create index idx_pbp_quote on product_bundle_product ( product_id ) create index idx_pbp_bq on product_bundle_product ( product_id, bundle_id );


CREATE TABLE product_bundle_quote (id varchar(36)  NOT NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL ,bundle_id varchar(36)  NULL ,quote_id varchar(36)  NULL ,bundle_index int  DEFAULT '0' NOT NULL  ) ALTER TABLE product_bundle_quote ADD CONSTRAINT pk_product_bundle_quote PRIMARY KEY (id) create index idx_pbq_bundle on product_bundle_quote ( bundle_id ) create index idx_pbq_quote on product_bundle_quote ( quote_id ) create index idx_pbq_bq on product_bundle_quote ( quote_id, bundle_id );


CREATE TABLE product_product (id varchar(36)  NOT NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL ,parent_id varchar(36)  NULL ,child_id varchar(36)  NULL  ) ALTER TABLE product_product ADD CONSTRAINT pk_product_product PRIMARY KEY (id) create index idx_pp_parent on product_product ( parent_id ) create index idx_pp_child on product_product ( child_id );


CREATE TABLE quotes_accounts (id varchar(36)  NOT NULL ,quote_id varchar(36)  NULL ,account_id varchar(36)  NULL ,account_role varchar(20)  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE quotes_accounts ADD CONSTRAINT pk_quotes_accounts PRIMARY KEY (id) create index idx_acc_qte_acc on quotes_accounts ( account_id ) create index idx_acc_qte_opp on quotes_accounts ( quote_id ) create index idx_quote_account_role on quotes_accounts ( quote_id, account_role );

CREATE TABLE quotes_audit (
    id varchar(36) NOT NULL DEFAULT '' ,
    parent_id varchar(36) NOT NULL DEFAULT '' ,
    date_created datetime NULL DEFAULT NULL ,
    created_by varchar(36) NULL DEFAULT NULL ,
    field_name varchar(100) NULL DEFAULT NULL ,
    data_type varchar(100) NULL DEFAULT NULL ,
    before_value_string varchar(255) NULL DEFAULT NULL ,
    after_value_string varchar(255) NULL DEFAULT NULL ,
    before_value_text text NULL DEFAULT NULL ,
    after_value_text text NULL DEFAULT NULL 
);

CREATE TABLE quotes_contacts (id varchar(36)  NOT NULL ,contact_id varchar(36)  NULL ,quote_id varchar(36)  NULL ,contact_role varchar(20)  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE quotes_contacts ADD CONSTRAINT pk_quotes_contacts PRIMARY KEY (id) create index idx_con_qte_con on quotes_contacts ( contact_id ) create index idx_con_qte_opp on quotes_contacts ( quote_id ) create index idx_quote_contact_role on quotes_contacts ( quote_id, contact_role );


CREATE TABLE quotes_opportunities (id varchar(36)  NOT NULL ,opportunity_id varchar(36)  NULL ,quote_id varchar(36)  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE quotes_opportunities ADD CONSTRAINT pk_quotes_opportunities PRIMARY KEY (id) create index idx_opp_qte_opp on quotes_opportunities ( opportunity_id ) create index idx_quote_oportunities on quotes_opportunities ( quote_id );


CREATE TABLE report_schedules (id varchar(36)  NOT NULL ,user_id varchar(36)  NOT NULL ,report_id varchar(36)  NOT NULL ,next_run datetime  NOT NULL ,active bit  DEFAULT '0' NOT NULL ,time_interval int  NULL ,date_modified datetime  NULL ,schedule_type varchar(3)  NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_start datetime  NULL) ALTER TABLE report_schedules ADD CONSTRAINT pk_report_schedules PRIMARY KEY (id);


CREATE TABLE category_tree (self_id varchar(36)  NULL ,node_id int  NOT NULL identity(1,1),parent_node_id int  DEFAULT '0' NULL ,type varchar(36)  NULL  ) ALTER TABLE category_tree ADD CONSTRAINT pk_category_tree PRIMARY KEY (node_id) create index idx_categorytree on category_tree ( self_id );


CREATE TABLE workflow_schedules (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,date_expired datetime  NOT NULL ,workflow_id varchar(36)  NULL ,target_module varchar(50)  NULL ,bean_id varchar(36)  NULL ,parameters varchar(255)  NULL  ) ALTER TABLE workflow_schedules ADD CONSTRAINT pk_workflow_schedules PRIMARY KEY (id) create index idx_wkfl_schedule on workflow_schedules ( workflow_id, deleted );


CREATE TABLE contracts_opportunities (id varchar(36)  NOT NULL ,opportunity_id varchar(36)  NULL ,contract_id varchar(36)  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE contracts_opportunities ADD CONSTRAINT pk_contracts_opportunities PRIMARY KEY (id) create index contracts_opp_alt on contracts_opportunities ( contract_id );


CREATE TABLE contracts_contacts (id varchar(36)  NOT NULL ,contact_id varchar(36)  NULL ,contract_id varchar(36)  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE contracts_contacts ADD CONSTRAINT pk_contracts_contacts PRIMARY KEY (id) create index contracts_contacts_alt on contracts_contacts ( contact_id, contract_id );


CREATE TABLE contracts_quotes (id varchar(36)  NOT NULL ,quote_id varchar(36)  NULL ,contract_id varchar(36)  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE contracts_quotes ADD CONSTRAINT pk_contracts_quotes PRIMARY KEY (id) create index contracts_quot_alt on contracts_quotes ( contract_id, quote_id );


CREATE TABLE contracts_products (id varchar(36)  NOT NULL ,product_id varchar(36)  NULL ,contract_id varchar(36)  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE contracts_products ADD CONSTRAINT pk_contracts_products PRIMARY KEY (id) create index contracts_prod_alt on contracts_products ( contract_id, product_id );


CREATE TABLE projects_quotes (id varchar(36)  NOT NULL ,quote_id varchar(36)  NULL ,project_id varchar(36)  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE projects_quotes ADD CONSTRAINT pk_projects_quotes PRIMARY KEY (id) create index idx_proj_quote_proj on projects_quotes ( project_id ) create index idx_proj_quote_quote on projects_quotes ( quote_id ) create index projects_quotes_alt on projects_quotes ( project_id, quote_id );


CREATE TABLE kbdocuments_views_ratings (id varchar(36)  NOT NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL ,kbdocument_id varchar(36)  NULL ,views_number int  DEFAULT '0' NULL ,ratings_number int  DEFAULT '0' NULL  ) ALTER TABLE kbdocuments_views_ratings ADD CONSTRAINT pk_kbdocuments_views_ratings PRIMARY KEY (id) create index idx_kbvr_kbdoc on kbdocuments_views_ratings ( kbdocument_id );


CREATE TABLE users_holidays (id varchar(36)  NOT NULL ,user_id varchar(36)  NULL ,holiday_id varchar(36)  NULL ,date_modified datetime  NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE users_holidays ADD CONSTRAINT pk_users_holidays PRIMARY KEY (id) create index idx_user_holi_user on users_holidays ( user_id ) create index idx_user_holi_holi on users_holidays ( holiday_id ) create index users_quotes_alt on users_holidays ( user_id, holiday_id );


CREATE TABLE report_cache (id varchar(36)  NOT NULL ,assigned_user_id varchar(36)  NOT NULL ,contents text  NULL ,report_options text  NULL ,deleted varchar(1) NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL  ) ALTER TABLE report_cache ADD CONSTRAINT pk_report_cache PRIMARY KEY (id, assigned_user_id, deleted);


CREATE TABLE dataset_layouts (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,parent_value varchar(50)  NULL ,layout_type varchar(25)  NOT NULL ,parent_id varchar(36)  NULL ,list_order_x int  NULL ,list_order_z int  NULL ,row_header_id varchar(36)  NULL ,hide_column varchar(3)  NULL  ) ALTER TABLE dataset_layouts ADD CONSTRAINT pk_dataset_layouts PRIMARY KEY (id) create index idx_datasetlayout on dataset_layouts ( parent_value, deleted );


CREATE TABLE dataset_attributes (id varchar(36)  NOT NULL ,deleted bit  DEFAULT '0' NOT NULL ,date_entered datetime  NOT NULL ,date_modified datetime  NOT NULL ,modified_user_id varchar(36)  NULL ,created_by varchar(36)  NULL ,display_type varchar(25)  NOT NULL ,display_name varchar(50)  NULL ,attribute_type varchar(8)  NOT NULL ,parent_id varchar(36)  NULL ,font_size varchar(8)  DEFAULT '0' NULL ,cell_size varchar(3)  NULL ,size_type varchar(3)  NULL ,bg_color varchar(25)  NULL ,font_color varchar(25)  NULL ,wrap varchar(3)  NULL ,style varchar(25)  NULL ,format_type varchar(25)  NOT NULL  ) ALTER TABLE dataset_attributes ADD CONSTRAINT pk_dataset_attributes PRIMARY KEY (id) create index idx_datasetatt on dataset_attributes ( parent_id, deleted );


CREATE TABLE session_history (id varchar(36)  NOT NULL ,session_id varchar(100)  NULL ,date_entered datetime  NULL ,date_modified datetime  NULL ,last_request_time datetime  NULL ,session_type varchar(100)  NULL ,is_violation bit  DEFAULT '0' NULL ,num_active_sessions int  DEFAULT '0' NULL ,deleted bit  DEFAULT '0' NOT NULL  ) ALTER TABLE session_history ADD CONSTRAINT pk_session_history PRIMARY KEY (id);

UPDATE [accounts] SET team_id = 1;
UPDATE [bugs] SET team_id = 1;
UPDATE [calls] SET team_id = 1;
UPDATE [campaigns] SET team_id = 1;
UPDATE [cases] SET team_id = 1;
UPDATE [contacts] SET team_id = 1;
UPDATE [contracts] SET team_id = 1;
UPDATE [documents] SET team_id = 1;
UPDATE [emails] SET team_id = 1;
UPDATE [email_templates] SET team_id = 1;
UPDATE [inbound_email] SET team_id = 1;
UPDATE [leads] SET team_id = 1;
UPDATE [meetings] SET team_id = 1;
UPDATE [notes] SET team_id = 1;
UPDATE [opportunities] SET team_id = 1;
UPDATE [project] SET team_id = 1;
UPDATE [project_task] SET team_id = 1;
UPDATE [prospect_lists] SET team_id = 1;
UPDATE [prospects] SET team_id = 1;
UPDATE [tasks] SET team_id = 1;
UPDATE [bugs] SET system_id = 1;
UPDATE [cases] SET system_id = 1;
UPDATE [folders] SET team_id = 1 WHERE is_group = '1';


CREATE NONCLUSTERED INDEX idx_contracts_primary on contracts_audit (id);
CREATE NONCLUSTERED INDEX idx_kbcontents_primary on kbcontents_audit (id);
CREATE NONCLUSTERED INDEX idx_products_primary on products_audit (id);
CREATE NONCLUSTERED INDEX idx_quotes_primary on quotes_audit (id);


CREATE NONCLUSTERED INDEX idx_contracts_parent_id on contracts_audit (parent_id);
CREATE NONCLUSTERED INDEX idx_kbcontents_parent_id on kbcontents_audit (parent_id);
CREATE NONCLUSTERED INDEX idx_products_parent_id on products_audit (parent_id);
CREATE NONCLUSTERED INDEX idx_quotes_parent_id on quotes_audit (parent_id);

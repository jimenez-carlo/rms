truncate tbl_batch;
truncate tbl_cron_log;
truncate tbl_lto_transmittal;
truncate tbl_fund_history;
truncate tbl_fund_transaction;
truncate tbl_misc;
truncate tbl_rerfo;
truncate tbl_topsheet;
truncate tbl_transmittal;
truncate tbl_transmittal_sales;
truncate tbl_voucher;

update tbl_fund set fund = 1000, cash_on_hand = 0, cash_on_check = 0;

-- update tbl_sales set status = 0, registration = 0, pending_date = 0, registration_date = 0, voucher = 0, fund = 0, rerfo = 0, topsheet = 0, file = 0;

truncate tbl_sales;
truncate tbl_customer;
truncate tbl_engine;
truncate rms_expense;

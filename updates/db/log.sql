begin transaction;
alter table settings add column uselog INTEGER DEFAULT 1;
alter table settings add column log_show_itemdata INTEGER DEFAULT 1;
alter table settings add column log_actions INTEGER DEFAULT 65535;
commit;

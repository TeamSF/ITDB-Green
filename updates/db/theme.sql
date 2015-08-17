begin transaction;
alter table settings add column theme default 'default';
commit;

-------------------------------------------------------------------------------------------------------
-- Need -------------
select a.mmgg,a.dat_ind,a.id_zone,a.value,(a.value-b.value) as demand,to_char(b.dat_ind,'dd.mm.YYYY')||'-'||to_char(a.dat_ind,'dd.mm.YYYY') as period from (
select *,DENSE_RANK() OVER (order by mmgg) as num from tmp_demand where mmgg::char(10)||dat_ind::char(10) in (
 select  mmgg::char(10)||date::char(10) from
 (select mmgg,max(dat_ind) as date from tmp_demand group by 1 order by 1 desc) e)
 ) a
 left join (
select *,DENSE_RANK() OVER (order by mmgg) as num from tmp_demand where mmgg::char(10)||dat_ind::char(10) in (
 select  mmgg::char(10)||date::char(10) from
 (select mmgg,max(dat_ind) as date from tmp_demand group by 1 order by 1 desc) e)
 ) b
on a.id_zone=b.id_zone and a.num=(b.num+1)
 order by mmgg desc,id_zone asc
 
 select mmgg,value,id_operation,id_zone,dat_ind,value_diff,type_meter into tmp_demand from (
 select n.id, n.dt, n.id_person, n.id_paccnt, n.dat_ind, n.id_meter, n.id_typemet, 
        n.carry, n.coef_comp, n.id_zone, n.num_eqp, n.id_energy, n.id_operation, 
        n.value, n.id_prev, n.value_diff, n.value_cons, n.id_ind, n.id_work, n.mmgg, 
        n.flock, n.id_corr, n.mmgg_corr, n.indic_real,
 m.name as type_meter,i2.value as p_indic,i2.dat_ind as p_dat_ind,
 CASE WHEN n.id_ind is null and n.id_work is null THEN 1 ELSE 0 END as is_manual,
 ph.num_pack, ww.id_work as id_hwork, w.idk_work, 
 coalesce(n.indic_real,pd.indic_real::int) as indic_real,
 case WHEN exists (select c.id from acm_indication_h as c where c.id_paccnt = 200023216 and c.id = n.id and c.mmgg_change <>n.mmgg ) THEN 1 ELSE 0 END as is_corrected,
 case WHEN not exists (select c.id from acm_indication_tbl as c where c.id_paccnt = 200023216 and c.mmgg =n.mmgg and c.id_zone = n.id_zone and c.dat_ind > n.dat_ind) THEN 1 ELSE 0 END as is_last,
 u1.name as user_name , 1 as ind_mode
 from acm_indication_tbl as n
 left join eqi_meter_tbl as m on (m.id = n.id_typemet)
 left join acm_indication_tbl as i2 on (i2.id = n.id_prev)
 left join ind_pack_data as pd on (n.id_ind = pd.id)
 left join ind_pack_header as ph on (pd.id_pack = ph.id_pack)
 left join clm_work_indications_tbl as ww on (ww.id = n.id_work)
 left join clm_works_tbl as w on (w.id = ww.id_work)
 left join syi_user as u1 on (u1.id = n.id_person)
 where n.id_paccnt = 200023216

union all

 select pd.id, pd.dt_input as dt, pd.id_person, pd.id_paccnt, coalesce(pd.dt_indic,ph.dt_pack,pd.work_period) as dat_ind,
        pd.id_meter, pd.id_type_meter as id_typemet, 
        pd.carry, pd.k_tr as coef_comp, pd.id_zone, pd. num_meter as num_eqp, pd.kind_energy as id_energy, pd.id_operation, 
        pd.indic as value, null as id_prev, null as value_diff, null as value_cons, null as id_ind, null as id_work, pd.work_period as mmgg, 
        pd.flock, null as id_corr, null as mmgg_corr, null as indic_real,
 m.name as type_meter,null as p_indic,null as p_dat_ind,
 0 as is_manual,
 ph.num_pack, null as id_hwork, null as idk_work, 
 null as indic_real,
 0 as is_corrected,
 0 as is_last,
 u1.name as user_name , 2 as ind_mode
  from 
   ind_pack_data as pd 
   join ind_pack_header as ph on (pd.id_pack = ph.id_pack)
   left join eqi_meter_tbl as m on (m.id = pd.id_type_meter)
   left join syi_user as u1 on (u1.id = pd.id_person)
   where id_operation is not null
   and id_operation in (16,26)
   and id_indic is null
   and pd.id_paccnt = 200023216
) as ss
order by mmgg desc



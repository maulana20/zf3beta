create table tblPeriod (
   period_begin          varchar(6)           identity,
   period_status         varchar(1)           null,
   constraint PK_TBLPERIOD primary key nonclustered (period_begin)
)
go

create table tblPaymentType (
   paymenttype_id           int                   identity,
   paymenttype_name         varchar(50)           null,
   constraint PK_TBLPAYMENTTYPE primary key nonclustered (paymenttype_id)
)
go

create table tblGroupAccount (
   groupaccount_id           int                   identity,
   groupaccount_name         varchar(50)           null,
   groupaccount_type         varchar(6)            null,
   groupaccount_parent       int                   null,
   constraint PK_TBLGROUPACCOUNT primary key nonclustered (groupaccount_id)
)
go

create table tblCoa (
   coa_id                int                 identity,
   coatype_id            int                 null,
   user_id               int                 null,
   coa_code              varchar(6)          null,
   coa_name              varchar(50)         null,
   coa_created           int                 null,
   lod                   int                 null,
   coa_desc              varchar(160)        null,
   groupaccount_id       int                 null,
   vou_code              varchar(6)          null,
   coa_status            varchar(1)          null,
   constraint PK_TBLCOA primary key nonclustered (coa_id)
)
go

create table tblCoaType (
   coatype_id                int                 identity,
   coatype_code              varchar(6)          null,
   coatype_name              varchar(50)         null,
   constraint PK_TBLCOATYPE primary key nonclustered (coatype_id)
)
go

create table tblPosting (
   posting_id               int                  identity,
   coa_id                   int                  null,
   period_begin             varchar(6)           null,
   period_balance           float                null,
   constraint PK_TBLPOSTING primary key nonclustered (posting_id)
)
go

create table tblGeneralCashBank (
   generalcashbank_id        int                 identity,
   financialtrans_id         int                 null,
   generalcashbank_position  varchar(1)          null,
   generalcashbank_status    varchar(1)          null,
   constraint PK_TBLGENERALCASHBANK primary key nonclustered (generalcashbank_id)
)
go

create table tblInterCashBank (
   intercashbank_id          int                 identity,
   financialtrans_out        int                 null,
   financialtrans_in         int                 null,
   intercashbank_status      varchar(1)          null,
   constraint PK_TBLINTERCASHBANK primary key nonclustered (intercashbank_id)
)
go

create table tblFinancialTrans (
   financialtrans_id        int                  identity,
   user_id                  int                  null,
   paymenttype_id           int                  null,
   period_begin             varchar(6)           null,
   financialtrans_date      int                  null,
   vou                      varchar(15)          null,
   financialtrans_status    varchar(1)           null,
   constraint PK_TBLFINANCIALTRANS primary key nonclustered (financialtrans_id)
)
go

create table tblGlAnalysis (
   glanalysis_id        int                  identity,
   financialtrans_id    int                  null,
   coa_to               int                  null,
   coa_from             int                  null,
   glanalysis_desc      varchar(160)         null,
   glanalysis_position  varchar(1)           null,
   glanalysis_value     float                null,
   constraint PK_TBLGLANALYSIS primary key nonclustered (glanalysis_id)
)
go

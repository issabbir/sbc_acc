USE [sbc_dev]
GO

DECLARE	@return_value int,
    @p_action_type varchar(1),
    @p_trans_detail_id bigint,
    @p_trans_master_id bigint,
    @p_gl_acc_id int,
    @p_dr_cr varchar(1),
    @p_currency_code varchar(5),
    @p_exchange_rate numeric(10, 4),
    @p_amount_type_id int,
    @p_amount_ccy numeric(20, 2),
    @p_amount_lcy numeric(20, 2),
    @p_without_cheque_yn varchar(1),
    @p_cheque_no varchar(10),
    @p_cheque_date date,
    @p_without_challan_yn varchar(1),
    @p_challan_type_id int,
    @p_challan_no varchar(25),
    @p_challan_date date,
    @p_gl_subsidiary_id int,
    @p_customer_id int,
    @p_vendor_id int,
    @p_emp_id int,
    @p_user_id int,
    @o_status_code int,
    @o_status_message varchar(100)

SELECT	@p_action_type = N'I'
SELECT	@p_trans_master_id = 202303000000145
SELECT	@p_gl_acc_id = 2000000075
SELECT	@p_dr_cr = N'D'
SELECT	@p_currency_code = N'BDT'
SELECT	@p_exchange_rate = 1
SELECT	@p_amount_type_id = 999999
SELECT	@p_amount_ccy = 100
SELECT	@p_amount_lcy = 100
SELECT	@p_gl_subsidiary_id = 214
SELECT	@p_user_id = 22109647

EXEC	@return_value = [SBCACC].[GL_TRANS_DETAIL_MAKE]
                        @p_action_type = @p_action_type OUTPUT,
                        @p_trans_detail_id = @p_trans_detail_id OUTPUT,
                        @p_trans_master_id = @p_trans_master_id OUTPUT,
                        @p_gl_acc_id = @p_gl_acc_id OUTPUT,
                        @p_dr_cr = @p_dr_cr OUTPUT,
                        @p_currency_code = @p_currency_code OUTPUT,
                        @p_exchange_rate = @p_exchange_rate OUTPUT,
                        @p_amount_type_id = @p_amount_type_id OUTPUT,
                        @p_amount_ccy = @p_amount_ccy OUTPUT,
                        @p_amount_lcy = @p_amount_lcy OUTPUT,
                        @p_without_cheque_yn = @p_without_cheque_yn OUTPUT,
                        @p_cheque_no = @p_cheque_no OUTPUT,
                        @p_cheque_date = @p_cheque_date OUTPUT,
                        @p_without_challan_yn = @p_without_challan_yn OUTPUT,
                        @p_challan_type_id = @p_challan_type_id OUTPUT,
                        @p_challan_no = @p_challan_no OUTPUT,
                        @p_challan_date = @p_challan_date OUTPUT,
                        @p_gl_subsidiary_id = @p_gl_subsidiary_id OUTPUT,
                        @p_customer_id = @p_customer_id OUTPUT,
                        @p_vendor_id = @p_vendor_id OUTPUT,
                        @p_emp_id = @p_emp_id OUTPUT,
                        @p_user_id = @p_user_id OUTPUT,
                        @o_status_code = @o_status_code OUTPUT,
                        @o_status_message = @o_status_message OUTPUT

SELECT	@p_action_type as N'@p_action_type',
          @p_trans_detail_id as N'@p_trans_detail_id',
          @p_trans_master_id as N'@p_trans_master_id',
          @p_gl_acc_id as N'@p_gl_acc_id',
          @p_dr_cr as N'@p_dr_cr',
          @p_currency_code as N'@p_currency_code',
          @p_exchange_rate as N'@p_exchange_rate',
          @p_amount_type_id as N'@p_amount_type_id',
          @p_amount_ccy as N'@p_amount_ccy',
          @p_amount_lcy as N'@p_amount_lcy',
          @p_without_cheque_yn as N'@p_without_cheque_yn',
          @p_cheque_no as N'@p_cheque_no',
          @p_cheque_date as N'@p_cheque_date',
          @p_without_challan_yn as N'@p_without_challan_yn',
          @p_challan_type_id as N'@p_challan_type_id',
          @p_challan_no as N'@p_challan_no',
          @p_challan_date as N'@p_challan_date',
          @p_gl_subsidiary_id as N'@p_gl_subsidiary_id',
          @p_customer_id as N'@p_customer_id',
          @p_vendor_id as N'@p_vendor_id',
          @p_emp_id as N'@p_emp_id',
          @p_user_id as N'@p_user_id',
          @o_status_code as N'@o_status_code',
          @o_status_message as N'@o_status_message'

SELECT	'Return Value' = @return_value

GO



CREATE
OR
ALTER PROCEDURE SBCACC.GL_TRANS_DETAIL_MAKE(
    @p_action_type VARCHAR (1) 			  	out
    ,@p_trans_detail_id BIGINT				out
    ,@p_trans_master_id BIGINT				out
    ,@p_gl_acc_id INT						out
    ,@p_dr_cr VARCHAR (1)					out
    ,@p_currency_code VARCHAR (5)			out
    ,@p_exchange_rate NUMERIC (10,4)		out
    ,@p_amount_type_id INT					out
    ,@p_amount_ccy NUMERIC (20,2)			out
    ,@p_amount_lcy NUMERIC (20,2)			out
    ,@p_without_cheque_yn VARCHAR (1)		out
    ,@p_cheque_no VARCHAR (10)				out
    ,@p_cheque_date DATE					out
    ,@p_without_challan_yn VARCHAR (1)		out
    ,@p_challan_type_id INT					out
    ,@p_challan_no VARCHAR (25)				out
    ,@p_challan_date DATE					out
    ,@p_gl_subsidiary_id INT				out
    ,@p_customer_id INT						out
    ,@p_vendor_id INT						out
    ,@p_emp_id INT							out
    --     ,@p_budget_dept_id INT				out
--     ,@p_budget_head_id INT				out
    ,@p_user_id INT							out
    ,@o_status_code varchar(MAX) OUTPUT
    ,@o_status_message VARCHAR (max) OUTPUT
    )
    AS
BEGIN
		--SET NOCOUNT ON;
		-------------
		--Variables--
		-------------
		DECLARE
@action_type varchar(1),
				@v_cost_center_dept_id int,
				@v_inactive_yn varchar(1),
				@v_postable_yn varchar(1),
				@v_gl_acc_name varchar(max),
				@v_live_deploy_policy_flag INT,
				@v_chq_validation_policy_flag INT,
				@v_cheque_leaf_used INT,
				@row_count int,

				@master_count int,
				@module_id INT,
				@function_id INT,
				@department_id INT,
				@bill_sec_id INT,
				@bill_reg_id INT,
		        @fiscal_year_id INT,
				@trans_period_id INT,
				@trans_period_name VARCHAR(10),--@trans_period_name INT,
				@trans_batch_id	BIGINT,
				@trans_date	DATE,
				@document_date DATE,
				@document_no VARCHAR(50),
				@document_ref VARCHAR(200),
				@narration VARCHAR(500),
				@reversal_trans_flag INT,
				@reversal_ref_trans_master_id BIGINT,
				@reversal_ref_trans_date DATE,
				@system_generated_yn VARCHAR(1),--@system_generated_yn INT,
-- 				@gl_trans_master INT,
				@unauthorized varchar(10),
				@v_party_account_id	bigint,
				@v_party_account_name VARCHAR(100),
				--@error_status varchar(10), --will implement later
				@trans_tracer_id BIGINT,
-- 				@gl_count INT,
				@TransactionName VARCHAR(20);

		SET @unauthorized = SBCACC.getConst('unauthorized');
		SET @TransactionName = 'DetailInsert';

        --BEGIN TRAN  @TransactionName
BEGIN
BEGIN TRY
SELECT --@master_count = count(*),
        @module_id = module_id
     , @function_id = function_id
     , @department_id = department_id
     , @bill_sec_id = bill_sec_id
     , @bill_reg_id = bill_reg_id
     , @fiscal_year_id = FISCAL_YEAR_ID
     , @trans_period_id = trans_period_id
     , @trans_period_name = trans_period_name
     , @trans_batch_id = trans_batch_id
     , @trans_date = trans_date
     , @document_date = document_date
     , @document_no = document_no
     , @document_ref = document_ref
     , @narration = narration
     , @reversal_trans_flag = reversal_trans_flag
     , @reversal_ref_trans_master_id = reversal_ref_trans_master_id
     , @reversal_ref_trans_date = reversal_ref_trans_date
     , @system_generated_yn = system_generated_yn
FROM SBCACC.FAS_GL_TRANS_MASTER
WHERE trans_master_id = @p_trans_master_id;

IF @@ROWCOUNT = 0
BEGIN
                    SET @o_status_code    = 1--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Master] - Invalid Input - Transaction Master ID is not valid. ';
                    RETURN;
END;
END TRY
BEGIN CATCH
BEGIN
                    SET @o_status_code    = 2--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Master] - Master information fetching failed.';
                    RETURN;
END;
END CATCH;
END;

		/*BEGIN
            SET @o_status_code    = SBCACC.getConst('error_status');
            SET @o_status_message = '[GL Trans Master] - I am here now. ';
            RETURN;
        END;*/
		SET @action_type = UPPER(ISNULL(@p_action_type, 'X'));
	    SET @v_live_deploy_policy_flag  = SBCACC.getLiveDeployPolicyFlag();
		SET @v_chq_validation_policy_flag = SBCACC.GET_CHQ_VALIDATION_POLICY_FLAG();

		-- Validate CRUD Action Type
		----------------------------
		IF @action_type <> SBCACC.getConst('insert_record')
BEGIN
				SET @o_status_code    = 3--SBCACC.getConst('error_status');
				SET @o_status_message = '[GL Trans Master] - Action Type is not valid! Action Type: ' + @action_type;
				RETURN;
END

		-- Validate Detail Entry
		------------------------

		IF  @p_trans_master_id IS NULL
BEGIN
				SET @o_status_code    = 4--SBCACC.getConst('error_status');
				SET @o_status_message = '[GL Trans Master] - Invalid Input - Transaction Master ID.';
				RETURN;
END

		-- FETCH TRANSACTION MASTER ENTRY
		---------------------------------
BEGIN TRY
            SELECT trans_master_id
            FROM SBCACC.fas_gl_trans_master
            WHERE trans_master_id = @p_trans_master_id

            IF @@ROWCOUNT = 0
                    BEGIN
                    SET @o_status_code    = 5--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Master] - Invalid Input - Transaction Master ID is not valid.';
                    RETURN;
                    END

            IF  @p_gl_acc_id IS NULL
                    BEGIN
                    SET @o_status_code    = 6--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Detail] - Invalid Input - GL Account ID';
                    RETURN;
                    END

            IF @v_postable_yn = SBCACC.getConst('no')
                    BEGIN
                    SET @o_status_code    = 7--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Detail] - Not a valid Post-able Account!';
                    RETURN;
                    END

            IF  @p_dr_cr IS NULL
                    BEGIN
                    SET @o_status_code    = 8--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Detail] - Invalid Input - Debit/Credit required!';
                    RETURN;
                    END;

            -- validate Currency
            -------------------------
            IF  @p_currency_code IS NULL
                    BEGIN
                    SET @o_status_code    = 9--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Detail] - Invalid Input - Currency required!';
                    RETURN;
                    END;

            IF  @p_currency_code = 'BDT' AND isnull(@p_exchange_rate, 0) <> 1
                    BEGIN
                    SET @o_status_code    = 10--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Detail] - Invalid Input - Currency exchange rate must be 1.00 for BDT.';
                    RETURN;
                    END;

            IF  @p_exchange_rate IS NULL
                    BEGIN
                    SET @o_status_code    = 11-SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Detail] - Invalid Input - Currency exchange rate required!';
                    RETURN;
                    END;
            ELSE IF @p_exchange_rate < 0
            BEGIN
                                SET @o_status_code    = 12--SBCACC.getConst('error_status');
                                SET @o_status_message = '[GL Trans Detail] - Invalid Input - Currency exchange rate must be positive number.';
                                RETURN;
            END;

                        -- validate negative amount
                        ---------------------------
                        IF  ISNULL(@p_amount_ccy, 0) <= 0 OR isnull(@p_amount_lcy, 0) <= 0 --OR @amount_ccy IS NULL OR @amount_lcy IS NULL
            BEGIN
                                SET @o_status_code    = 13--SBCACC.getConst('error_status');
                                SET @o_status_message = '[GL Trans Detail] - Invalid Input - Transaction Amount must be positive number. Amount ccy : ' +
                                                  @p_amount_ccy + ' Amount lcy : ' + @p_amount_lcy + ' GL ID ' +
                                                  @p_gl_acc_id;
                                RETURN;
            END;
END TRY
begin catch
    BEGIN
        SET @o_status_code    = 'QQ'+CAST(@p_trans_master_id AS varchar(MAX))--SBCACC.getConst('error_status');
        SET @o_status_message = '[GL Trans Master] - Invalid Input - Transaction Master ID is not valid. '+ ERROR_MESSAGE();
        RETURN;
    END;
end catch

		------------------------------
		-- Validate GL Account
		-----------------------------
BEGIN
SELECT --@row_count = count(gl_acc_id),
        @v_cost_center_dept_id = a.cost_center_dept_id
     , @v_inactive_yn = a.inactive_yn
     , @v_postable_yn = a.postable_yn
     , @v_gl_acc_name = a.gl_acc_name
FROM SBCACC.fas_gl_coa a
WHERE gl_acc_id = @p_gl_acc_id;

IF  @@ROWCOUNT = 0
BEGIN
                    SET @o_status_code    = 14--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Detail] - Invalid Input - GL Account ID: ' + @p_gl_acc_id;
                    RETURN;
END;

			-- Check GL Account is Post-able
			IF  @v_postable_yn = SBCACC.getConst('no')
BEGIN
                    SET @o_status_code    = 15--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Detail] - Invalid Input - GL Account is not Post-able! ID: ' + @p_gl_acc_id;
                    RETURN;
END;

			-- Check GL Account is Inactive
			IF  @v_inactive_yn = SBCACC.getConst('yes')
BEGIN
                    SET @o_status_code    = 16--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Detail] - GL Account is Inactive! ID: ' + @p_gl_acc_id;
                    RETURN;
END;

			-- Check Dept/Cost Center for GL Account
			IF  @department_id IS NOT NULL
				IF @v_cost_center_dept_id IS NOT NULL AND @v_cost_center_dept_id <> @department_id AND @function_id != SBCACC.getConst('func_gl_journal_year_end')
BEGIN
                        SET @o_status_code    = 17--SBCACC.getConst('error_status');
                        SET @o_status_message = '[GL Trans Detail] - GL Account ID does not match with Dept/Center!';
                        RETURN;
END;
END;
		-- CASH/BANK ACCOUNT VALIDATION
		--------------------------------
		-- Validate GL Receive Voucher
		IF  @module_id = SBCACC.getConst('fas_gl_module')
			AND @function_id IN (SBCACC.getConst('func_gl_bank_receive'), SBCACC.getConst('func_gl_cash_receive'))
			AND @p_dr_cr = SBCACC.getConst('credit')
			AND isnull(@reversal_trans_flag, 0) = 0
BEGIN
            SET @row_count = 0;

SELECT gl_acc_id --@row_count = COUNT(*)
FROM SBCACC.fas_gl_cash_acc_params
WHERE gl_acc_id = @p_gl_acc_id;

IF  @@ROWCOUNT > 0
BEGIN
                    SET @o_status_code = 18--SBCACC.getConst('error_status');
                    -------------------------------------------------------------------------------
                    --Cash gl_account to Cash gl_account transfer only allowed in transfer module--
                    -------------------------------------------------------------------------------
                    SET @o_status_message = '[GL Trans Detail] - Invalid Input - Contra Cash Account is not allowed in Receive Voucher. ID: ' + @p_gl_acc_id;
                    RETURN;
END;

            -- Validate GL Payment Voucher
            IF  @module_id = SBCACC.getConst('fas_gl_module')
               AND @function_id IN (SBCACC.getConst('func_gl_bank_payment'), SBCACC.getConst('func_gl_cash_payment'))
               AND @p_dr_cr = SBCACC.getConst('debit')
               AND isnull(@reversal_trans_flag, 0) = 0

                --SET @row_count = 0;

SELECT gl_acc_id--@row_count = COUNT(*)
FROM SBCACC.fas_gl_cash_acc_params
WHERE gl_acc_id = @p_gl_acc_id;

IF  @@ROWCOUNT > 0
BEGIN
                    SET @o_status_code    = 19--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Detail] - Invalid Input - Contra Cash Account is not allowed in Payment Voucher. ID: ' + @p_gl_acc_id;
                    RETURN;
END;
END;

        -- Journal voucher
        /*
        IF @module_id = SBCACC.getConst('fas_gl_module
           AND @function_id = SBCACC.getConst('func_gl_journal_voucher
        THEN
            SELECT COUNT(*)
            INTO   v_count
            FROM   fas_gl_acc_params_cash t
            WHERE  t.gl_acc_id = @gl_acc_id
            AND    t.cash_acc_type_id = SBCACC.getConst('bank_account_type; -- bank account

            IF v_count > 0
               AND @v_live_deploy_policy_flag <> SBCACC.getConst('fas_state_data_migration -- [1=Live,0=Backlog, 2=Data Migration]
            THEN
                @o_status_code    := SBCACC.getConst('error_status;
                @o_status_message := '[GL Trans Detail] - Invalid Input - Bank Account is not allowed in Journal Voucher. ID: ' ||
                                      @gl_acc_id;
                RETURN;
            END IF;
        END IF;
        */

		-----------------------------------------------
		-- CHEQUE/CHALLAN VALIDATION
		-----------------------------------------------
		IF  @module_id = SBCACC.getConst('fas_gl_module')
		   AND @function_id = SBCACC.getConst('func_gl_cash_receive')
		   AND isnull(@reversal_trans_flag, 0) = 0

            -- CHALLAN VALIDATION
            ---------------------
            IF @p_dr_cr = SBCACC.getConst('debit')
                IF (@p_challan_type_id IS NULL OR @p_challan_no IS NULL OR @p_challan_date IS NULL) AND @p_without_challan_yn = SBCACC.getConst('no')
BEGIN
                        SET @o_status_code    = 20--SBCACC.getConst('error_status');
                        SET @o_status_message = '[GL Trans Detail] -  Invalid Input - Challan Information is required for Cash/Bank Account Debit!';
                        RETURN;
END;
ELSE IF @p_dr_cr = SBCACC.getConst('credit')
                IF @p_challan_no IS NOT NULL
                   OR @p_challan_date IS NOT NULL
                   OR @p_challan_type_id IS NOT NULL
BEGIN
                        SET @o_status_code    = 21--SBCACC.getConst('error_status');
                        SET @o_status_message = '[GL Trans Detail] - Invalid Input - Challan Information NOT required for Cash/Bank Account Credit!';
                        RETURN;
END;
ELSE IF (@module_id = SBCACC.getConst('fas_gl_module') AND @function_id = SBCACC.getConst('func_gl_bank_payment'))
			  OR (@module_id = SBCACC.getConst('fas_cm_module') AND @function_id = SBCACC.getConst('func_cm_clearing_reconciliation'))

			-- CHEQUE VALIDATION
			--------------------
			IF @p_dr_cr = SBCACC.getConst('credit') AND ISNULL(@reversal_trans_flag, 0) = 0
				IF (@p_cheque_no IS NULL OR @p_cheque_date IS NULL) AND (@p_without_challan_yn = SBCACC.getConst('no'))
BEGIN
						SET @o_status_code    = 22--SBCACC.getConst('error_status');
                        SET @o_status_message = '[GL Trans Detail] - Invalid Input - Cheque Information required for Cash/Bank Account Credit!';
                        RETURN;
END;
ELSE IF @v_chq_validation_policy_flag = SBCACC.getConst('true_flag')
BEGIN
SELECT --@row_count = count(*),
       @v_cheque_leaf_used = isnull(t.chq_leaf_used_flag, 0)
FROM fas_cm_cheque_leaf t
WHERE t.bank_gl_acc_id = @p_gl_acc_id
  AND t.chq_leaf_no = @p_cheque_no;

IF  @@ROWCOUNT = 0
BEGIN
                                SET @o_status_code    = 23--SBCACC.getConst('error_status');
                                SET @o_status_message = '[GL Trans Detail] - Invalid Input - Cheque No not found! Cheque No: ' + @p_cheque_no;
                                RETURN;
END;

                        IF  @v_cheque_leaf_used = SBCACC.getConst('used')
BEGIN
                                SET @o_status_code    = 24--SBCACC.getConst('error_status');
                                SET @o_status_message = '[GL Trans Detail] - Invalid Input - Cheque No already Used!';
                                RETURN;
END;
ELSE
                            -- MARK CHEQUE USED
                            ---------------------
                            SET @v_cheque_leaf_used = SBCACC.getConst('used');
END;
ELSE IF @p_dr_cr = SBCACC.getConst('debit') AND isnull(@reversal_trans_flag, 0) = 0
			    IF @p_cheque_no IS NOT NULL OR @p_cheque_date IS NOT NULL
BEGIN
                        SET @o_status_code    = 25--SBCACC.getConst('error_status');
                        SET @o_status_message = '[GL Trans Detail] - Invalid Input - Cheque Information NOT required for Cash/Bank Account Debit';
                        RETURN;
END;
ELSE IF @module_id = SBCACC.getConst('fas_gl_module') AND @function_id = SBCACC.getConst('func_gl_tfr_bank_to_bank')
			-- CHEQUE VALIDATION
			----------------------
			IF @p_dr_cr = SBCACC.getConst('credit') AND isnull(@reversal_trans_flag, 0) = 0
				IF (@p_cheque_no IS NULL OR @p_cheque_date IS NULL) AND @p_without_cheque_yn = SBCACC.getConst('no')
BEGIN
                        SET @o_status_code    = 26--SBCACC.getConst('error_status');
                        SET @o_status_message = '[GL Trans Detail] - Invalid Input - Cheque Information required for Cash/Bank Account Credit';
                        RETURN;
END;
ELSE IF @p_dr_cr = SBCACC.getConst('debit') AND isnull(@reversal_trans_flag, 0) = 0
                IF @p_cheque_no IS NOT NULL OR @p_cheque_date IS NOT NULL
BEGIN
                        SET @o_status_code    = 27--SBCACC.getConst('error_status');
                        SET @o_status_message = '[GL Trans Detail] - Invalid Input - Cheque Information NOT required for Cash/Bank Account Debit';
                        RETURN;
END;
ELSE IF @module_id = SBCACC.getConst('fas_gl_module') AND @function_id = SBCACC.getConst('func_gl_journal_voucher')
			-- check cheque entry
			IF @p_cheque_no IS NOT NULL OR @p_cheque_date IS NOT NULL
BEGIN
                    SET @o_status_code    = 28--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Detail] - Invalid Input - Cheque Information NOT required for Non-Cash/Bank Transaction!';
                    RETURN;
END;

			-- check challan entry
			IF @p_challan_no IS NOT NULL OR @p_challan_date IS NOT NULL OR @p_challan_type_id IS NOT NULL
BEGIN
                    SET @o_status_code    = 29--SBCACC.getConst('error_status');
                    SET @o_status_message = '[GL Trans Detail] - Invalid Input - Challan Information NOT required for Non-Cash/Bank Transaction!';
                    RETURN;
END;

		-- Validate Control Account (for GL Subsidiary)
		--------------------------------------------------
		IF @p_gl_subsidiary_id IS NULL AND @module_id = SBCACC.getConst('fas_gl_module')
BEGIN
                --SET @row_count = 0;
SELECT t.GL_ACC_ID --@row_count = count(*)
FROM SBCACC.fas_gl_subsidiary_params t
WHERE t.gl_acc_id = @p_gl_acc_id IF @row_count > 0 AND @v_live_deploy_policy_flag <> SBCACC.getConst('fas_state_data_migration') -- [1=Live,0=Backlog, 2=Data Migration]

                IF @@ROWCOUNT > 0
BEGIN
                        set @o_status_code    = 30--SBCACC.getConst('error_status');
                        set @o_status_message = '[GL Trans Detail] - Transaction not allowed in GL Subsidiary Control Account! Account ID: ' + @p_gl_acc_id;
                        RETURN;
END;
END;

		----------------------------------------------
		-- Validate Authorized Balance for GL Account
		---------------------------------------------
		--DECLARE @val_o_status_code int output;
EXEC SBCACC.VALIDATE_BALANCE_GL_ACCOUNT @p_gl_acc_id, @p_dr_cr, @p_amount_lcy, @o_status_code, @o_status_message;

		IF  @o_status_code <> SBCACC.getConst('success_status')
		   RETURN;

		-- Update GL Balance
		---------------------
EXEC SBCACC.UPDATE_BALANCE_GL_ACCOUNT @p_gl_acc_id ,@p_dr_cr ,@p_amount_ccy ,@p_amount_lcy ,@trans_period_id , @unauthorized, @o_status_code, @o_status_message;

		IF  @o_status_code <> SBCACC.getConst('success_status')
			--THROW @o_status_code, @o_status_message, 1;
            RETURN;

		-- UPDATE GL SUBSIDIARY ACCOUNT BALANCE
		------------------------------------------
		IF @p_gl_subsidiary_id IS NOT NULL
BEGIN
				-- VALIDATE GL SUBSIDIARY BALANCE
				---------------------------------
EXEC SBCACC.VALIDATE_BALANCE_GL_SUBSIDIARY @p_gl_subsidiary_id ,@p_customer_id ,@p_vendor_id ,@p_emp_id ,@p_dr_cr ,@p_amount_lcy ,@o_status_code ,@o_status_message;

				IF @o_status_code <> SBCACC.getConst('success_status')
					--THROW @o_status_code, @o_status_message, 1;
				    RETURN;

EXEC SBCACC.UPDATE_BALANCE_GL_SUBSIDIARY @p_gl_acc_id, @p_gl_subsidiary_id, @p_customer_id ,@p_vendor_id ,@p_emp_id ,@p_dr_cr ,@p_amount_ccy ,@p_amount_lcy ,@unauthorized ,@v_party_account_id ,@v_party_account_name ,@o_status_code,@o_status_message;

				-- Update transaction Header Information
				----------------------------------------
				IF @o_status_code <> SBCACC.getConst('success_status')
					--THROW @o_status_code, @o_status_message, 1;
				    RETURN;
ELSE
BEGIN TRY
UPDATE SBCACC.fas_gl_trans_master
SET party_account_id   = @v_party_account_id
  , party_account_name = @v_party_account_name
  , vendor_id          = @p_vendor_id
  , customer_id        = @p_customer_id
  , emp_id             = @p_emp_id
WHERE trans_master_id = @p_trans_master_id;
END TRY
BEGIN CATCH
                        --ROLLBACK TRAN @TransactionName;
SET @o_status_code = 31--SBCACC.getConst('error_status');
                        SET @o_status_message = '[GL Trans Detail] - Transaction failed :'+ERROR_NUMBER()+': '+ERROR_MESSAGE();
                        RETURN;
END CATCH;
END;

		-- Update budget booking utilization
		------------------------------------
		--IF @p_budget_head_id IS NOT NULL
		--	BEGIN
		--	DECLARE @GL_MODULE
		--	-- Call the procedure
		--	EXEC SBCACC.BUDGET_BOOKING_UTILIZE @trans_period_id
		--									 ,@budget_dept_id
		--									 ,@budget_head_id
		--									 ,@amount_lcy
		--									 ,SBCACC.getConst('fas_gl_module')
		--									 ,NULL
		--									 ,NULL
		--									 ,@o_status_code
		--									 ,@o_status_message;
		--	END;

		-- Update transaction Header Information
		----------------------------------------
		IF @p_cheque_no IS NOT NULL AND isnull(@reversal_trans_flag, 0) = 0
BEGIN TRY
UPDATE SBCACC.fas_gl_trans_master
SET cheque_no   = @p_cheque_no
  , cheque_date = @p_cheque_date
WHERE trans_master_id = @p_trans_master_id;
END TRY
BEGIN CATCH
                --ROLLBACK TRAN @TransactionName;
SET @o_status_code = 32--SBCACC.getConst('error_status');
                SET @o_status_message = '[GL Trans Detail] - Transaction failed :'+ERROR_NUMBER()+': '+ERROR_MESSAGE();
                RETURN;
END CATCH;

		IF @p_challan_no IS NOT NULL AND isnull(@reversal_trans_flag, 0) = 0
BEGIN TRY
UPDATE SBCACC.fas_gl_trans_master
SET challan_no      = @p_challan_no
  , challan_date    = @p_challan_date
  , challan_type_id = @p_challan_type_id
WHERE trans_master_id = @p_trans_master_id;
END TRY
BEGIN CATCH
                --ROLLBACK TRAN @TransactionName;
SET @o_status_code = 33--SBCACC.getConst('error_status');
                SET @o_status_message = '[GL Trans Detail] - Transaction failed :'+ERROR_NUMBER()+': '+ERROR_MESSAGE();
                RETURN;
END CATCH;

		-- Mark Cheque Leaf Used/Unused
		------------------------------------
		IF isnull(@reversal_trans_flag, 0) = 0
		    AND @v_cheque_leaf_used = SBCACC.getConst('used')-- [0=Unused,1=Used]
            AND @v_live_deploy_policy_flag = SBCACC.getConst('fas_state_live_operation')
BEGIN TRY
				-- fas_cm_cheque_leaf
UPDATE SBCACC.fas_cm_cheque_leaf
SET chq_leaf_used_flag = SBCACC.getConst('used') -- [0=UNUSED, 1=USED]
  , trans_detail_id    = @p_trans_detail_id
  , trans_date         = @trans_date
  , cheque_date        = @p_cheque_date
  , cheque_amount      = @p_amount_lcy
WHERE bank_gl_acc_id = @p_gl_acc_id
  AND chq_leaf_no = @p_cheque_no;
END TRY
BEGIN CATCH
                --ROLLBACK TRAN @TransactionName;
SET @o_status_code = 34--SBCACC.getConst('error_status');
                SET @o_status_message = '[GL Trans Detail] - Transaction failed :'+ERROR_NUMBER()+': '+ERROR_MESSAGE();
                RETURN;
END CATCH;

		-- Transaction Detail Entry
		---------------------------
		DECLARE @ID BIGINT, @SET_TRANS_DETAIL_ID BIGINT
BEGIN
EXEC SBCACC.GL_GENERATE_TRANS_DETAIL_ID @ID  OUTPUT
SELECT @SET_TRANS_DETAIL_ID = @ID

    SET @p_trans_detail_id = @SET_TRANS_DETAIL_ID

--SET @p_trans_detail_id = generate_trans_detail_id;
DECLARE @V_TRANS_BATCH_ID VARCHAR(MAX),
				@SET_V_TRANS_BATCH_ID VARCHAR(MAX)

			EXEC SBCACC.GENERATE_BATCH_TRACER_SEQ @trans_period_id, 'SEQ_POSTING_TRACER_ID', @V_TRANS_BATCH_ID OUTPUT;
SELECT @SET_V_TRANS_BATCH_ID =  cast(@V_TRANS_BATCH_ID as bigint)

    SET @trans_tracer_id = @SET_V_TRANS_BATCH_ID
--SET @o_status_message = @trans_tracer_id
--SET @o_status_code = 123
--return;

/*DECLARE @val_trans_tracer_id varchar(max), @SET_V_TRANS_BATCH_ID varchar(max);
EXEC SBCACC.GENERATE_BATCH_TRACER_SEQ @trans_period_id, 'SEQ_POSTING_TRACER_ID', @val_trans_tracer_id;

SELECT @SET_V_TRANS_BATCH_ID =  cast(@val_trans_tracer_id as BIGINT)
--SELECT @SET_V_TRANS_BATCH_ID =  cast(@val_trans_tracer_id as NUMERIC(15))

SET @trans_tracer_id = @SET_V_TRANS_BATCH_ID*/
END;

		IF @p_amount_type_id IS NULL
BEGIN
				SET @p_amount_type_id = SBCACC.getConst('amount_type_id_general'); -- General (Others)
END;
BEGIN TRY

INSERT INTO SBCACC.fas_gl_trans_detail(TRANS_DETAIL_ID
			                                       , TRANS_MASTER_ID
			                                       , MODULE_ID
			                                       , FUNCTION_ID
			                                       , TRANS_PERIOD_ID
			                                       , TRANS_PERIOD_NAME
			                                       , TRANS_BATCH_ID
			                                       , TRANS_TRACER_ID
			                                       , TRANS_DATE
			                                       , DEPARTMENT_ID
			                                       , BILL_SEC_ID
			                                       , BILL_REG_ID
			                                       , DOCUMENT_DATE
			                                       , DOCUMENT_NO
			                                       , DOCUMENT_REF
			                                       , GL_ACC_ID
			                                       , DR_CR
			                                       , CURRENCY_CODE
			                                       , EXCHANGE_RATE
			                                       , AMOUNT_TYPE_ID
			                                       , AMOUNT_CCY
			                                       , AMOUNT_LCY
			                                       , NARRATION
			                                       , CHEQUE_NO
			                                       , CHEQUE_DATE
			                                       , CHALLAN_NO
			                                       , CHALLAN_DATE
			                                       , PARTY_ACCOUNT_ID
			                                       , PARTY_ACCOUNT_NAME
			                                       , GL_SUBSIDIARY_ID
			                                       , CUSTOMER_ID
			                                       , VENDOR_ID
			                                       , EMP_ID
			                                       --, PARTY_BALANCE_CCY
			                                       --, PARTY_BALANCE_LCY
			                                       , REVERSAL_TRANS_FLAG
			                                       , REVERSAL_REF_TRANS_DATE
			                                       , REVERSAL_REF_TRANS_MASTER_ID
			                                       , INSERT_BY
			                                       , INSERT_DATE
			                                       --, BUDGET_HEAD_ID
			                                       --, OLD_VENDOR_ID
			                                       , FISCAL_YEAR_ID
			                                       --, BUDGET_DEPT_ID
			                                       )
			VALUES(
			       @p_trans_detail_id --BIGINT
			       , @p_trans_master_id --BIGINT
			       , @module_id --INT
			       , @function_id --INT
			       , @trans_period_id --INT
			       , @trans_period_name --VARCHAR
			       , @trans_batch_id --BIGINT
			       , @trans_tracer_id --BIGINT
			       , @trans_date --date
			       , @department_id --INT
			       , @bill_sec_id --INT
			       , @bill_reg_id --INT
			       , @document_date --date
			       , @document_no --VARCHAR
			       , @document_ref --VARCHAR
			       , @p_gl_acc_id --int
			       , @p_dr_cr --VARCHAR
			       , @p_currency_code --VARCHAR
			       , @p_exchange_rate --NUMERIC
			       , @p_amount_type_id --int
			       , @p_amount_ccy --NUMERIC
			       , @p_amount_lcy --NUMERIC
			       , @narration --VARCHAR
			       , @p_cheque_no --VARCHAR
			       , @p_cheque_date --date
			       , @p_challan_no --VARCHAR
			       , @p_challan_date --date
			       , @v_party_account_id --BIGINT
			       , @v_party_account_name  --VARCHAR
			       , @p_gl_subsidiary_id --int
			       , @p_customer_id --int
			       , @p_vendor_id --int
			       , @p_emp_id --BIGINT
			       , @reversal_trans_flag --int
			       , @reversal_ref_trans_date --date
			       , @reversal_ref_trans_master_id --BIGINT
			       , @p_user_id --int
			       , GETDATE()
			       , @fiscal_year_id --int
			       );

			-- SUCCESS MESSAGE
			------------------
			--COMMIT TRANSACTION @TransactionName;
			SET @o_status_code    = 35--SBCACC.getConst('success_status');
			SET @o_status_message = '[GL Trans Detail] - Transaction Saved Successfully. Batch ID: ' + CAST(@trans_batch_id as varchar(max));
END TRY
BEGIN CATCH
			--ROLLBACK TRAN @TransactionName;
SET @o_status_code    = 36--SBCACC.getConst('error_status');
			SET @o_status_message = '[GL Trans Detail] - DETAIL TRANSACTION ENTRY FAILED! ' +CAST(ERROR_LINE() AS VARCHAR(50))+'-'+ ERROR_MESSAGE();
			RETURN;
END CATCH;
END;

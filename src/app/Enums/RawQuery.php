<?php


namespace App\Enums;

/***
 * To register Raw Queries*
 */

class RawQuery
{
    public const GET_ACTIVE_NEWS = "SELECT
                                isnull(N.TITLE, N.TITLE_BN) AS TITLE,
                                isnull(N.TITLE_BN, N.TITLE) AS TITLE_BN,
                                isnull(N.DESCRIPTION, N.DESCRIPTION_BN) AS DESCRIPTION,
                                isnull(N.DESCRIPTION_BN, N.DESCRIPTION) AS DESCRIPTION_BN,
                                N.CREATED_AT,
                                U.USER_NAME AS CREATED_USERNAME,
                                N.UPDATED_AT,
                                N.UPDATED_BY,
                                N.NEWS_ID,
                                N.NEWS_STATUS_ID,
                                N.ATTACHMENT_FILENAME,
                                N.ACTIVE_FROM,
                                N.ACTIVE_TO,
                                N.ENABLED_YN,
                                N.SORT_ORDER,
                                S.STATUS,
                                S.STATUS_KEY
                            FROM
                                APP_SECURITY.GEN_NEWS  AS N
                                    INNER JOIN APP_SECURITY.GEN_NEWS_STATUS  AS S ON (S.NEWS_STATUS_ID = N.NEWS_STATUS_ID)
                                    LEFT JOIN APP_SECURITY.SEC_USERS  AS U        ON (U.USER_ID = N.CREATED_BY)
                                    LEFT JOIN APP_SECURITY.SEC_USERS  AS U2       ON (U2.USER_ID = N.UPDATED_BY)
                            WHERE
                                    upper(N.ENABLED_YN) = 'Y' AND
                                    S.STATUS_KEY = 'NEWS_PUBLISHED' AND
                                    (CAST(N.ACTIVE_FROM as Date)) <=cast(getDate() As Date) AND
                                (CAST(N.ACTIVE_TO as Date) > cast(getDate() As Date) OR N.ACTIVE_TO IS NULL)
                            ORDER BY N.SORT_ORDER";
}

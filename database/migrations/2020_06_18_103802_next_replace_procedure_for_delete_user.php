<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NextReplaceProcedureForDeleteUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS delete_user');
        DB::unprepared('
                CREATE PROCEDURE `delete_user`(id INT)
                BEGIN
                    DECLARE count INT default 0;
                    DECLARE EXIT HANDLER FOR SQLEXCEPTION
                        BEGIN
                            SHOW ERRORS;
                            ROLLBACK;
                        END;

                        START TRANSACTION;
                        IF
                            (SELECT
                                (SELECT count(*) FROM auctions WHERE (user_id = id OR last_bid_user_id = id) AND cancelled_at IS NULL AND end_at > NOW())
                                + (SELECT count(*) FROM adverts WHERE (user_id = id OR respond_user_id = id) AND cancelled_at IS NULL AND end_at > NOW())
                                + (SELECT count(*) FROM meetings WHERE (user_id = id OR seller_id = id) AND (status = 1 OR status = 2) AND meeting_date > NOW())
                            ) > 0
                        THEN
                            SET count = -1;
                        ELSE
                            UPDATE
                                users u
                            SET
                                u.phone=CONCAT(u.phone,"_",UNIX_TIMESTAMP(NOW())),
                                u.nickname=NULL,
                                u.slug=NULL,
                                u.email=null,
                                u.subscribers_count=0,
                                u.subscribes_count=0,
                                u.blocked_count=0,
                                u.meetings_rating=0.00,
                                u.deleted_at=now()
                            WHERE
                                u.id=id;
                            SET count = count + ROW_COUNT();

                            UPDATE oauth_access_tokens oat SET oat.revoked=true WHERE oat.user_id=id;
                            SET count = count + ROW_COUNT();

                            UPDATE meetings m SET m.deleted_at=now() WHERE m.seller_id=id;
                            SET count = count + ROW_COUNT();

                            UPDATE media m SET m.deleted_at=now() WHERE m.user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM profiles WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM user_meetings_options WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM user_photo_verifications WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            UPDATE users u SET subscribers_count=IF(u.subscribers_count > 0, u.subscribers_count - 1, 0) WHERE (SELECT count(*) FROM subscribes s WHERE s.user_id=u.id AND s.subscriber_id=id);
                            SET count = count + ROW_COUNT();

                            UPDATE users u SET subscribes_count=IF(u.subscribes_count > 0, u.subscribes_count - 1, 0) WHERE (SELECT count(*) FROM subscribes s WHERE s.user_id=id AND s.subscriber_id=u.id);
                            SET count = count + ROW_COUNT();

                            DELETE FROM subscribes WHERE user_id=id OR subscriber_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM blocked_users WHERE user_id=id OR blocked_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM want_with_you WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM subscriber_user_publications WHERE owner_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM notifications WHERE notifiable_type=\'users\' and notifiable_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM media_users_views WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM meetings_reviews WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM feed_viewed WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM oauth_access_tokens WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                        END IF;
                    COMMIT;
                    ROLLBACK;
                SELECT count;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS delete_user');
        DB::unprepared('
                CREATE PROCEDURE `delete_user`(id INT)
                BEGIN
                    DECLARE count INT default 0;
                    DECLARE EXIT HANDLER FOR SQLEXCEPTION
                        BEGIN
                            SHOW ERRORS;
                            ROLLBACK;
                        END;

                        START TRANSACTION;
                        IF
                            (SELECT
                                (SELECT count(*) FROM auctions WHERE (user_id = id OR last_bid_user_id = id) AND cancelled_at IS NULL AND end_at > NOW())
                                + (SELECT count(*) FROM adverts WHERE (user_id = id OR respond_user_id = id) AND cancelled_at IS NULL AND end_at > NOW())
                                + (SELECT count(*) FROM meetings WHERE (user_id = id OR seller_id = id) AND (status = 1 OR status = 2) AND meeting_date > NOW())
                            ) > 0
                        THEN
                            SET count = -1;
                        ELSE
                            UPDATE
                                users u
                            SET
                                u.phone=CONCAT(u.phone,"_",UNIX_TIMESTAMP(NOW())),
                                u.nickname=NULL,
                                u.slug=NULL,
                                u.email=null,
                                u.subscribers_count=0,
                                u.subscribes_count=0,
                                u.blocked_count=0,
                                u.meetings_rating=0.00,
                                u.deleted_at=now()
                            WHERE
                                u.id=id;
                            SET count = count + ROW_COUNT();

                            UPDATE users_private_chat_room_messages upcrm SET upcrm.deleted_at=now() WHERE upcrm.user_id=id;
                            SET count = count + ROW_COUNT();

                            UPDATE oauth_access_tokens oat SET oat.revoked=true WHERE oat.user_id=id;
                            SET count = count + ROW_COUNT();

                            UPDATE meetings m SET m.deleted_at=now() WHERE m.seller_id=id;
                            SET count = count + ROW_COUNT();

                            UPDATE media m SET m.deleted_at=now() WHERE m.user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM profiles WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM user_meetings_options WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM user_photo_verifications WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            UPDATE users u SET subscribers_count=IF(u.subscribers_count > 0, u.subscribers_count - 1, 0) WHERE (SELECT count(*) FROM subscribes s WHERE s.user_id=u.id AND s.subscriber_id=id);
                            SET count = count + ROW_COUNT();

                            UPDATE users u SET subscribes_count=IF(u.subscribes_count > 0, u.subscribes_count - 1, 0) WHERE (SELECT count(*) FROM subscribes s WHERE s.user_id=id AND s.subscriber_id=u.id);
                            SET count = count + ROW_COUNT();

                            DELETE FROM subscribes WHERE user_id=id OR subscriber_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM blocked_users WHERE user_id=id OR blocked_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM want_with_you WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM subscriber_user_publications WHERE owner_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM notifications WHERE notifiable_type=\'users\' and notifiable_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM media_users_views WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM meetings_reviews WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM feed_viewed WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                            DELETE FROM oauth_access_tokens WHERE user_id=id;
                            SET count = count + ROW_COUNT();

                        END IF;
                    COMMIT;
                    ROLLBACK;
                SELECT count;
            END;
        ');
    }
}

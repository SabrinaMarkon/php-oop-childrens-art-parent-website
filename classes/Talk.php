<?php
/**
Handles Forum Posting.
PHP 5
@author Sabrina Markon
@copyright 2017 Sabrina Markon, PHPSiteScripts.com
@license LICENSE.md
 **/
class Talk
{
    private $username;
    private $subject;
    private $message;

    function getAllPosts($postid) {

        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "select * from talk where parent_id='$postid' order by id";
        $q = $pdo->prepare($sql);
        $q->execute();
        $children = $q->rowCount();
        if ($children > 0) {
            echo '<ul class="root_post">';
            $q->setFetchMode(PDO::FETCH_ASSOC);
            $posts = $q->fetchAll();
            foreach ($posts as $post) {

                $badge = 0;
                $sql = "select * from talk where parent_id='" . $post['id'] . "' order by id";
                $q = $pdo->prepare($sql);
                $q->execute();
                $badge = $q->rowCount();

                echo "<li>" . $post['id'] . " - " . $post['subject'];
                if ($badge > 0) {
                    echo  " " . $badge;
                    $this->getAllPosts($post['id']);
                }
                echo "</li>";
            }
            echo "</ul>";
        }
        Database::disconnect();
    }

    function addPost($username,$neworreply) {

        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $parent_id = 0;

        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "insert into talk (parent_id, username, subject, message, dateadded) values (?, ?, ?, ?, NOW())";
        $q = $pdo->prepare($sql);
        $q->execute(array($parent_id, $username, $subject, $message));

        if ($parent_id === 0) {
            $show = 'Topic';
        } else {
            $show = 'Reply';
        }

        Database::disconnect();

        return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Your " . $show . " Was Added!</strong></div>";

    }

    function editPost($id) {

    }

    function deletePost($id) {

    }


}
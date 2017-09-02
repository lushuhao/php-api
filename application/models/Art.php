<?php
/**
 * Created by PhpStorm.
 * User: lu
 * @desc 文章操作Model类
 * Date: 2017/8/31
 * Time: 21:19
 */

class ArtModel {
    public $errno = 0;
    public $errmsg = "";
    private $_db = null;

    public function __construct() {
        $this->_db = new PDO( "mysql:host=127.0.0.1;dbname=imooc;", "root", "342623" );
        // 不设置下面这行的话，PDO会在拼SQL时候，把int 0 转成 string 0
        $this->_db->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
    }

    public function add( $title, $contents, $author, $cate, $artId = 0 ) {
        $isEdit = false;
        if ( $artId != 0 && is_numeric( $artId ) ) {
            /* 编辑文章 */
            $query = $this->_db->prepare( "select count(*) from `art` where `id`= ? " );
            $query->execute( array($artId) );
            $ret = $query->fetchAll();
            if ( !$ret || count( $ret ) != 1 ) { // 有且只有一个id
                $this->errno = -2004;
                $this->errmsg = "找不到你想要的文章！";
                return false;
            }
            $isEdit = true;
        } else {
            /* 新增文章 */
            /**
             * 检查Cate是否存在
             * 如果是编辑文章，cate之前创建过，不需要再做检验
             */
            $query = $this->_db->prepare( "select count(*) from `cate` WHERE `id` = ? " );
            $query->execute( array($cate) );
            $ret = $query->fetchAll();
            if ( !$ret || $ret[0][0] == 0 ) {
                $this->errno = -2005;
                $this->errmsg = "找不到对应ID的分类信息， cate id:".$cate.", 请先创建该分类。";
                return false;
            }
        }

        /**
         * 插入或更新文章内容
         */
        $data = array($title, $contents, $author, intval( $cate ));
        if ( !$isEdit ) {
            $query = $this->_db->prepare( "insert into `art` (`title`, `contents`, `author`, `cate`) VALUES ( ?, ?, ?, ?)" );
        } else {
            $query = $this->_db->prepare( "update `art` set `title`=?, `contents`=?, `author`=?, `cate`=? WHERE `id`=?" );
            $data[] = $artId; // 给数组添加一个artId
        }
        $ret = $query->execute( $data );
        if ( !$ret ) {
            $this->errno = -2006;
            $this->errmsg = "操作文章数据表失败，ErrInfo:".end( $query->errorInfo() ); // end()返回数组最后一位，errorInfo：0，1，2 => error message
            return false;
        }

        /**
         * 返回文章最后的ID值
         */
        if ( !$isEdit ) { // 插入新文章
            return intval( $this->_db->lastInsertId() ); // 最后一次操作数据库的id
        } else {
            return intval( $artId );
        }
    }

    public function del( $artId ) {
        $query = $this->_db->prepare( "delete from `art` WHERE  `id`=? " );
        $ret = $query->execute( array(intval( $artId )) );
        if ( !$ret ) {
            $this->errno = -2007;
            $this->errmsg = "删除失败，ErrInfo:".end( $query->errorInfo() );
            return false;
        }
        return true;
    }

    public function status( $artId, $status = 'offline' ) {
        $query = $this->_db->prepare( "update `art` set `status`=? WHERE  `id`=? " );
        $ret = $query->execute( array($status, intval( $artId )) );
        if ( !$ret ) {
            $this->errno = -2008;
            $this->errmsg = "更新文章状态失败，ErrInfo:".end( $query->errorInfo() );
            return false;
        }
        return true;
    }

    public function get( $artId ) {
        $query = $this->_db->prepare( "select * from `art` WHERE  `id`=? " );
        $status = $query->execute( array(intval( $artId )) );
        $ret = $query->fetchAll();
        if ( !$status || !$ret ) {
            $this->errno = -2009;
            $this->errmsg = "查询失败，ErrInfo:".end( $query->errorInfo() );
            return false;
        }
        $artInfo = $ret[0];
        /**
         * 获取分类信息
         */
        $query = $this->_db->prepare( "select `name` from `cate` WHERE `id`=?" );
        $query->execute( array($artInfo['cate']) );
        $ret = $query->fetchAll();
        if ( !$ret ) {
            $this->errno = -2010;
            $this->errmsg="获取分类信息失败，ErrInfo:".end($query->errorInfo());
            return false;
        }
        $artInfo['cateName'] = $ret[0]['name']; // 获取到分类信息添加到数组

        $data = array(
            'id' => intval($artId),
            'title'=>$artInfo['title'],
            'contents'=>$artInfo['contents'],
            'author'=>$artInfo['author'],
            'cateName'=>$artInfo['cateName'],
            'cateId'=>intval($artInfo['cate']),
            'ctime'=>$artInfo['ctime'],
            'mtime'=>$artInfo['mtime'],
            'status'=>$artInfo['status'],
        );
        return $data;
    }
}
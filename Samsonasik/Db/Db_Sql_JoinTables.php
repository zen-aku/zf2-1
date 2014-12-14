<?php
/*
 * Zend Framework 2 has a component named Zend\Db that can simplify a way application interact with database. 
 * If you just has one table, you don’t need Sql statement, but if you have many, you need this. 
 * With Sql object, your paradigm of Query will be changed to Object Oriented Paradigm.
 */

// For example, i have an AlbumTable :
namespace Test\Model;
 
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
 
class AlbumTable extends AbstractTableGateway {
    protected $table ='album';
 
    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->initialize();
    }
}

// Then, i have a TrackTable which a child of AlbumTable :
namespace Test\Model;
 
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
 
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
 
class TrackTable extends AbstractTableGateway {
    protected $table ='tracks';
 
    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->initialize();
    }
 
	// We will join 2 tables, that the function join will be placed in TrackTable class, so we add the following :
    public function getTrackByAlbumId( $id = 9 ) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from($this->table)->join('album', 'tracks.album_id = album.id');
        $where = new  Where();
        $where->equalTo('album_id', $id) ;
        $select->where($where);
 
        // you can check your query by echo-ing :
        // echo $select->getSqlString();
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
}
/*
How about Classes relation ?
Until Zend Framework version 2.0.0rc3, and as i know, $_dependentTables and $_referenceMap as ZF1’s way to relate more than one Table Class is not yet supported. So why i have to create 2 classes? it’s because i want to show you how to discipline your technique at building application.
Conclusions
You can use Zend\Db\Sql\Sql for Query or joining tables and change your Query paradigm to Object Oriented Paradigm
Zend\Db\Sql can join your tables, but can’t “create a relation” like ZF1’s way yet
[UPDATE]
*/
// You can use Simple usage like the following closure:
use Zend\Db\Sql\Select; 

public function getTrackByAlbumId($id=9) {
    $result  = $this->select(
		function (Select $select) use ($id){
			$select->where(array('album_id'=>$id));
			$select->join('album', 'tracks.album_id = album.id');
		}
	);   
    return $result;
}
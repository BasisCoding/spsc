<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class TransaksiModel extends CI_Model {
	
		function get_no_trx()
		{
			$q = $this->db->query("SELECT MAX(RIGHT(kode_transaksi,3)) AS kd_max FROM transaksi WHERE DATE(date)=CURDATE()");
	        $kd = "";
	        if($q->num_rows()>0){
	            foreach($q->result() as $k){
	                $tmp = ((int)$k->kd_max)+1;
	                $kd = sprintf("%04s", $tmp);
	            }
	        }else{
	            $kd = "0001";
	        }
	        date_default_timezone_set('Asia/Jakarta');
	        return date('dmy').$kd;
		}

		function proses_transaksi($data)
		{
			return $this->db->insert('transaksi', $data);
		}

		function transaksi_pending()
		{
			$this->db->select('transaksi.*, barang.nama_barang, barang.harga');
			$this->db->from('transaksi');
			$this->db->join('barang', 'barang.id = transaksi.id_barang', 'left');
			$this->db->where('status_transaksi', 0);
			$this->db->where('id_konsumen', $this->session->userdata('id'));
			return $this->db->get()->result();
		}

		function transaksi_sukses()
		{
			$this->db->select('transaksi.*, barang.nama_barang, barang.harga');
			$this->db->from('transaksi');
			$this->db->join('barang', 'barang.id = transaksi.id_barang', 'left');
			$this->db->where('status_transaksi', 1);
			$this->db->where('id_konsumen', $this->session->userdata('id'));
			return $this->db->get()->result();
		}

		function get_transaksi($kode_transaksi)
		{
			$this->db->select('transaksi.*, barang.*');
			$this->db->from('transaksi');
			$this->db->join('barang', 'barang.id = transaksi.id_barang', 'left');
			$this->db->where('transaksi.kode_transaksi', $kode_transaksi);
			return $this->db->get()->row();
		}

		function transaksi_pending_admin()
		{
			$this->db->select('transaksi.*, barang.nama_barang, barang.harga, barang.stok, barang.id as id_barang');
			$this->db->from('transaksi');
			$this->db->join('barang', 'barang.id = transaksi.id_barang', 'left');
			$this->db->where('status_transaksi', 0);
			return $this->db->get()->result();
		}

		function transaksi_sukses_admin()
		{
			$this->db->select('transaksi.*, barang.nama_barang, barang.harga, barang.stok, barang.id as id_barang');
			$this->db->from('transaksi');
			$this->db->join('barang', 'barang.id = transaksi.id_barang', 'left');
			$this->db->where('status_transaksi', 1);
			return $this->db->get()->result();
		}

		function validasi($id, $data, $id_barang, $barang)
		{
			$this->db->update('transaksi', $data, array('id' => $id));
			$this->db->update('barang', $barang, array('id' => $id_barang));
		}

		function chartPenjualanKategori($kategori)
		{
			$this->db->select('SUM(transaksi.jumlah) as total, MONTH(transaksi.date) as bulan, YEAR(transaksi.date) as tahun, barang.id_kategori');
			$this->db->from('transaksi');
			$this->db->join('barang', 'barang.id = transaksi.id_barang', 'left');
			if ($kategori != '') {
				$this->db->group_start();
					$this->db->where('id_kategori', $kategori);
				$this->db->group_end();
			}
			$this->db->where('status_transaksi', 1);
			$this->db->or_where('transaksi.date', date('Y'));
			$this->db->group_by('bulan, tahun');
			return $this->db->get()->result();
		}

	
	}
	
	/* End of file TransaksiModel.php */
	/* Location: ./application/models/TransaksiModel.php */
?>
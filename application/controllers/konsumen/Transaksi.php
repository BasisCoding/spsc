<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Transaksi extends CI_Controller {

		public function __construct()
		{
			parent::__construct();
			$this->load->model('TransaksiModel');
		}
	
		public function tambah_transaksi()
		{
			$data['id_barang'] = $this->input->post('id');
			$data['jumlah'] = $this->input->post('jumlah');
			$data['id_konsumen'] = $this->session->userdata('id');
			$data['date'] = date('Y-m-d H:i:s');
			$data['status_transaksi'] = 0;
			$data['kode_transaksi'] = $this->TransaksiModel->get_no_trx();
			$proses = $this->TransaksiModel->proses_transaksi($data);

			$kode = $data['kode_transaksi'];
			if ($proses) {
				$response = array(
					'status' => 'success',
					'message' => 'Pembelian Berhasil, Mengaliahkan Ke Transaksi',
					'redirect' => site_url('konsumen/Transaksi/transaksi_detail/'.$kode)
				);
			}else{
				$response = array(
					'status' => 'error',
					'message' => 'Pembelian Gagal, Coba Lagi!',
					'redirect' => base_url('konsumen/Produk/')
				);
			}

			echo json_encode($response);
		}

		public function transaksi_pending()
		{
			$this->load->view('_partials/_head');
			$this->load->view('_partials/_header');
			$this->load->view('_partials/_sidebar');
			$this->load->view('konsumen/transaksi_pending');
			$this->load->view('_partials/_footer');
			$this->load->view('_partials/_plugin');
			$this->load->view('services/transaksi');
		}

		public function view_trx_pending()
		{
			$html = '';
			$data = $this->TransaksiModel->transaksi_pending();
			echo $this->db->last_query($data);
			$no = 1;
			foreach ($data as $dp) {
				
				$html .= '<tr>
							<td class="align-middle text-center">'.$no++.'</td>
							<td class="align-middle">'.$dp->kode_transaksi.'</td>
							<td class="align-middle">'.$dp->nama_barang.'</td>
							<td class="align-middle">'.$dp->jumlah.'</td>
							<td class="align-middle">'.date('d-m-Y', strtotime($dp->date)).'</td>
							
							<td class="align-middle">
								<button class="btn btn-sm btn-success text-center update-kategori mr-1 ml-lg-1 mb-1" data-id="'.$dp->id.'" data-kode="'.$dp->kode_transaksi.'">Lihat Transaksi</button>
							</td>
						</tr>';
			}
			echo $html;
		}
	
	}
	
	/* End of file Transaksi.php */
	/* Location: ./application/controllers/konsumen/Transaksi.php */
?>
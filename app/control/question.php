<?php

class question extends Controller
{
    public function index()
    {
        if ($this->helper('Login')->access([1, 2])) {
            $data['title'] = 'Soal Psikotest';
            $data['layout_type'] = 'sidebar';
            $data['plugins'] = 'basic|fontawesome|scrollbar';
            $data['jscript'] = 'question.funct';
            $this->view('template/base.head', $data);
            $this->view('template/user.navbar', $data);
            $this->view('template/user.sidebar', $data);
            $this->view('data/soal');
            $this->view('template/user.footer');
            $this->view('template/base.foot', $data);
        } else {
            Redirect('dashboard');
        }
    }
}

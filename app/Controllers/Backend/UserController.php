<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use Myth\Auth\Models\UserModel;
use Myth\Auth\Entities\User;

class UserController extends BaseController
{
    protected $userModel;
    protected $db;
    protected $config;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->config = config('Auth');
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'users' => $this->userModel->findAll(),
        ];
        return view('backend/user/index', $data);
    }

    public function create()
    {
        $groupModel = new \Myth\Auth\Models\GroupModel();
        $data = [
            'groups' => $groupModel->findAll()
        ];
        return view('backend/user/form', $data);
    }

    public function store()
    {
        $rules = [
            'email'    => 'required|valid_email|is_unique[users.email]',
            'username' => 'required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username]',
            'password' => 'required|strong_password',
            'pass_confirm' => 'required|matches[password]',
            'role'     => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $allowedPostFields = array_merge(['password'], $this->config->validFields, $this->config->personalFields);
        $user = new User($this->request->getPost($allowedPostFields));

        $user->activate();

        if (!$this->userModel->save($user)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        $userId = $this->userModel->getInsertID();

        // Assign Role
        $groupModel = new \Myth\Auth\Models\GroupModel();
        $roleId = $this->request->getPost('role');
        $groupModel->addUserToGroup($userId, $roleId);

        return redirect()->to('admin/users')->with('message', 'User berhasil dibuat.');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }
        
        $groupModel = new \Myth\Auth\Models\GroupModel();
        $groups = $groupModel->findAll();
        $userGroups = $groupModel->getGroupsForUser($id);
        
        $data = [
            'user' => $user,
            'groups' => $groups,
            'userGroups' => $userGroups
        ];
        return view('backend/user/form', $data);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);

        $rules = [
            'email'    => "required|valid_email|is_unique[users.email,id,$id]",
            'username' => "required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username,id,$id]",
            'role'     => 'required'
        ];
        
        if ($this->request->getPost('password')) {
            $rules['password'] = 'required|strong_password';
            $rules['pass_confirm'] = 'required|matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user->email = $this->request->getPost('email');
        $user->username = $this->request->getPost('username');
        
        if ($this->request->getPost('password')) {
            $user->password = $this->request->getPost('password');
        }

        if (!$this->userModel->save($user)) {
             return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }
        
        // Update Role
        $groupModel = new \Myth\Auth\Models\GroupModel();
        $groupModel->removeUserFromAllGroups($id);
        
        $roleId = $this->request->getPost('role');
        $groupModel->addUserToGroup($id, $roleId);
        
        return redirect()->to('admin/users')->with('message', 'User berhasil diperbarui.');
    }

    public function delete($id)
    {
        // Prevent deleting SuperAdmin or Self if needed
        if (user_id() == $id) {
             return redirect()->to('admin/users')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($this->userModel->delete($id, true)) {
             return redirect()->to('admin/users')->with('message', 'User berhasil dihapus.');
        }
        return redirect()->to('admin/users')->with('error', 'Gagal menghapus user.');
    }
}

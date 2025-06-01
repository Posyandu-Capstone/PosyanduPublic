import bcrypt

password = "test113"  # Password yang ingin di-hash
hashed_password = bcrypt.hashpw(password.encode(), bcrypt.gensalt())

print(hashed_password.decode())  # Simpan hasil hash ke database
input_password = "test113"  # Password dari user
stored_hash = hashed_password  # Hash yang disimpan di database

if bcrypt.checkpw(input_password.encode(), stored_hash):
    print("Password benar!")
else:
    print("Password salah!")

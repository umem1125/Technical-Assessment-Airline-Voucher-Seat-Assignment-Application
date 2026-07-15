import { useState } from 'react'
import axios from 'axios'
import './App.css'

const API_BASE_URL = 'http://127.0.0.1:8000/api'

function App() {
  const [formData, setFormData] = useState({
    crewName: '',
    crewId: '',
    flightNumber: '',
    flightDate: '',
    aircraftType: ''
  })

  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [successSeats, setSuccessSeats] = useState([])

  // handle input form
  const handleChange = (e) => {
    const {name, value} = e.target
    setFormData((prev) => ({
      ...prev,
      [name]: value
    }))
  }

  // generate voucher
  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true)
    setError('')
    setSuccessSeats([])

    const payloadCheck = {
      flightNumber: formData.flightNumber,
      date: formData.flightDate,
    }

    try {
      const checkRes = await axios.post(`${API_BASE_URL}/check`, payloadCheck)

      if (checkRes.data.exists) {
        setError(`Vouchers already have been generated for flight ${formData.flightNumber} on ${formData.flightDate}.`)
        setLoading(false)
        return
      }

      // generate voucher if have not data yet
      const payloadGenerate = {
        name: formData.crewName,
        id: formData.crewId,
        flightNumber: formData.flightNumber,
        date: formData.flightDate,
        aircraft: formData.aircraftType,
      }

      const generateRes = await axios.post(`${API_BASE_URL}/generate`, payloadGenerate)

      if (generateRes.data.data && generateRes.data.data.success) {
        setSuccessSeats(generateRes.data.data.seats)
      } else {
        setError('Failed to generate seats. Please try again.')
      }
    } catch (error) {
      if (error.response && error.response.data && error.response.data.message) {
        setError(error.response.data.message)
      } else {
        setError('Something went wrong.')
      } 
    } finally {
      setLoading(false)
    }
  }

  return (
    <>
      <div className='min-h-screen bg-grey-100 flex items-center justify-center p-6'>
        <div className='bg-white p-8 rounded-lg shadow-md w-full max-w-lg'>
          <h1 className='text-2xl font-bold text-gray-800 mb-6 text-center'>
            Airline Voucher Seat Assignment
          </h1>

          <form onSubmit={handleSubmit} className='space-y-4'>
            {/* crew name */}
            <div>
              <label className="block text-sm font-medium text-gray-700">Crew Name</label>
              <input 
                type="text"
                name='crewName'
                value={formData.crewName}
                onChange={handleChange}
                required
                className='mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500'
                placeholder='e.g. John Doe'
                />
            </div>

            {/* crew id */}
            <div>
              <label className="block text-sm font-medium text-gray-700">Crew ID</label>
              <input 
                type="text"
                name='crewId'
                value={formData.crewId}
                onChange={handleChange}
                required
                className='mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500'
                placeholder='e.g. 98123'
                />
            </div>

            {/* flight number */}
            <div>
              <label className="block text-sm font-medium text-gray-700">Flight Number</label>
              <input 
                type="text"
                name='flightNumber'
                value={formData.flightNumber}
                onChange={handleChange}
                required
                className='mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500'
                placeholder='e.g. GA102'
                />
            </div>

            {/* flight date */}
            <div>
              <label className="block text-sm font-medium text-gray-700">Flight Date</label>
              <input 
                type="date"
                name='flightDate'
                value={formData.flightDate}
                onChange={handleChange}
                required
                className='mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500'
                />
            </div>

            {/* aircraft type */}
            <div>
              <label className="block text-sm font-medium text-gray-700">Aircraft Type</label>
              <select
                name='aircraftType'
                value={formData.aircraftType}
                onChange={handleChange}
                required
                className='mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500'
              > 
                <option value="">-- Select Aircraft --</option>
                <option value="ATR">ATR</option>
                <option value="Airbus 320">Airbus 320</option>
                <option value="Boeing 737 Max">Boeing 737 Max</option>
              </select>
            </div>

            <button
              type='submit'
              disabled={loading}
              className={`w-full text-white font-medium cursor-pointer py-2 px-4 rounded-md transition duration-200 ${
                  loading ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'
                }`}
            >
              {loading ? 'Processing..' : 'Generate Voucher'}
            </button>
          </form>

          {/* error alert */}
          {error && (
            <div className='mt-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded'>
              <p className='font-semibold'>Error</p>
              <p className='text-sm'>{error}</p>
            </div>
          )}

          {/* success alert */}
          {successSeats.length > 0 && (
            <div className='mt-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded'>
              <p className="font-semibold text-lg mb-2">Vouchers Generated Successfully!</p>
              <p className="text-sm mb-3">The following 3 unique seats have been assigned:</p>
              <div className='flex gap-2'>
                {successSeats.map((seat, index) => (
                  <span
                    key={index}
                    className='bg-green-200 text-green-800 font-bold px-3 py-1 rounded text-center min-w-[60px]'
                  >
                    {seat}
                  </span>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>
    </>
  )
}

export default App
